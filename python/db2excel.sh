#!/bin/bash

(
    if [ -f ".db2excel_create" ]; then

        PATH_SCRIPT=$(dirname "$0")
        cd $PATH_SCRIPT

        # Carica il file .env e rende le variabili disponibili
        set -a
        source .env
        set +a

        export PATH=$PATH:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin

        echo ""
        echo ""
        echo "############################################################"
        echo "Script - START: $(date)"
        echo "------------------------------------------------------------"

        IMAGE_NAME="db2excel"
        CONTAINER_NAME=$IMAGE_NAME
        NETWORK_NAME="db2excel_network"
        SUBNET="192.168.1.0/24"
        CONTAINER_IP="192.168.1.110"
        DOCKERFILE="Dockerfile"

        # Funzione per controllare se l'immagine è aggiornata
        needs_build() {
          if [ ! -f ".docker_image_built" ]; then
            # Primo build
            return 0
          fi

          # Controlla la data di modifica del Dockerfile rispetto al file di stato
          if [ "$DOCKERFILE" -nt ".docker_image_built" ]; then
            return 0
          fi

          return 1
        }

        # Compila l'immagine solo se necessario
        if needs_build; then
            echo "Costruzione dell'immagine Docker $IMAGE_NAME..."
            docker build -t $IMAGE_NAME .
            # Aggiorna il timestamp del file di stato
            touch .docker_image_built

            # Se esiste già un container, fermalo ed eliminalo
            if [ "$(docker ps -aq -f name=$CONTAINER_NAME)" ]; then
                echo "Arresto ed eliminazione del vecchio container $CONTAINER_NAME..."
                docker stop $CONTAINER_NAME
                docker rm $CONTAINER_NAME
            fi

    #        # Avvia il nuovo container con restart always
    #        echo "Avvio del nuovo container $CONTAINER_NAME..."
    #        docker run -d \
    #            --name $CONTAINER_NAME \
    #            -v "$(pwd)/../.env":/app/.laravel-env \
    #            -v "$(pwd)":/app \
    #            $IMAGE_NAME
        else
            echo "L'immagine Docker $IMAGE_NAME è aggiornata."
        fi

        # Esegui il container
        if [ "$1" == "" ]; then
            # Crea la rete Docker, se non esiste
            if ! docker network ls | grep -q $NETWORK_NAME; then
              docker network create --subnet=$SUBNET $NETWORK_NAME
            fi

            docker run \
                --rm \
                --net $NETWORK_NAME \
                --ip $CONTAINER_IP \
                -v "$(pwd)/../.env":/app/.laravel-env \
                -v "$(pwd)":/app \
                $IMAGE_NAME \
                python main.py

            # Rimuovi la rete Docker dopo l'esecuzione
            docker network rm $NETWORK_NAME
        fi

        echo "------------------------------------------------------------"
        echo "Script - END: $(date)"

        rm -fr ".db2excel_create"

    fi
)
