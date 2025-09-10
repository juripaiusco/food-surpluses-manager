#FROM python:3.13.1-slim
FROM python:3.9.20-slim

# Installa librerie
RUN pip install \
    mysql-connector-python \
    load_dotenv \
    python-dotenv \
    openpyxl \
    pandas

RUN apt-get update \
    && apt-get install -y \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Imposta la directory di lavoro
WORKDIR /app
