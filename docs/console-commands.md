# Comandi Artisan custom

Documentazione dei comandi console definiti in `app/Console/Commands/`. Non esistono queue in questo progetto: sono tutti comandi lanciati manualmente o via cron esterno (vedi `CLAUDE.md`).

---

## mod-jobs-settings:export

Esporta righe di `mod_jobs_settings` (report/sezioni dei Jobs dinamici) in un file JSON versionabile in git, per poterle sincronizzare fra le installazioni delle varie sedi (Schio/Thiene/Bassano) tramite [`mod-jobs-settings:import`](#mod-jobs-settingsimport).

### Perché esiste

Ogni sede ha il proprio database, indipendente dalle altre. Il deploy porta solo il codice, non i dati: una riga di `mod_jobs_settings` creata/modificata in un ambiente (es. un nuovo report) non compare automaticamente nelle altre sedi. Questo comando produce un file JSON che può essere committato e portato con sé nel deploy; l'altra sede lo importa con `mod-jobs-settings:import`.

### Sintassi

```bash
php artisan mod-jobs-settings:export {--id=* : ID delle righe da esportare (ripetibile)} \
                                      {--all : Esporta tutte le righe} \
                                      {--out=database/data/mod_jobs_settings.json : Path del file JSON di output}
```

Serve almeno uno tra `--id` e `--all`.

### Esempi

```bash
# Esporta una sola riga (es. il nuovo report id 38)
php artisan mod-jobs-settings:export --id=38

# Esporta più righe in un colpo solo
php artisan mod-jobs-settings:export --id=38 --id=40 --id=41

# Esporta tutta la tabella
php artisan mod-jobs-settings:export --all

# Output su un file diverso da quello di default
php artisan mod-jobs-settings:export --id=38 --out=database/data/report-cf.json
```

### Comportamento

- Ogni riga esportata include: `uuid, type, title, description, query, schema, dynamic`.
- **Non** include `id` né `user_id`: sono valori locali al DB di origine, non portabili tra installazioni diverse.
- Se una riga non ha ancora `uuid` (es. righe create prima dell'introduzione di questa colonna), il comando lo genera e lo salva sulla riga stessa prima di esportarla — è il comando stesso a "battezzare" righe vecchie.
- Se il file di output esiste già, le nuove righe vengono **unite** a quelle già presenti (merge per `uuid`), non sovrascritte: si può accumulare più export nello stesso file nel tempo.
- Stampa una tabella riassuntiva (id locale, uuid, type, title) di quanto esportato.

### Dopo l'export

1. Verificare il diff del file JSON generato.
2. Committare il file (es. `database/data/mod_jobs_settings.json`).
3. Deployare il codice nelle sedi interessate.
4. Lanciare `mod-jobs-settings:import --apply` in ciascuna sede target.

---

## mod-jobs-settings:import

Importa in `mod_jobs_settings` le righe contenute in un file JSON prodotto da [`mod-jobs-settings:export`](#mod-jobs-settingsexport). È l'operazione simmetrica: applica in una sede quanto pubblicato da un'altra.

### Sintassi

```bash
php artisan mod-jobs-settings:import {--file=database/data/mod_jobs_settings.json : Path del file JSON da importare} \
                                      {--apply : Applica le modifiche (default: dry-run)}
```

### Esempi

```bash
# Dry-run: mostra cosa verrebbe creato/aggiornato, senza scrivere nulla
php artisan mod-jobs-settings:import

# Applica davvero le modifiche
php artisan mod-jobs-settings:import --apply

# Importa da un file diverso da quello di default
php artisan mod-jobs-settings:import --file=database/data/report-cf.json --apply
```

### Comportamento

- Matching per **`uuid`**, non per `id` (l'`id` numerico è locale a ogni database e può già essere occupato da una riga diversa in ogni sede):
  - `uuid` già presente nel DB corrente → **update** dei campi (`type, title, description, query, schema, dynamic`).
  - `uuid` assente → **insert** di una nuova riga (con `id` locale nuovo, assegnato automaticamente dal DB).
- **Idempotente**: rilanciare lo stesso file più volte non crea duplicati, aggiorna solo.
- Di default è un **dry-run**: stampa una tabella (azione `create`/`update`, uuid, type, title) senza scrivere nulla nel DB. Serve `--apply` per salvare davvero.
- Righe del JSON senza `uuid` vengono ignorate con un warning (file corrotto o non generato da `mod-jobs-settings:export`).

### Nota

Il comando non viene lanciato automaticamente dal deploy (`deploy.sh` è fuori da questo repository e non è gestito da qui): va eseguito manualmente in ogni sede dopo il deploy del codice, oppure agganciato a mano nello script di deploy.

---

## mod-jobs:fix-dynamic-groups

Ripara i gruppi dinamici (es. "componenti famiglia") di `customers_mod_jobs.values` la cui chiave base è rimasta un array vuoto `"[]"` invece di essere eliminata o rimpiazzata dal primo componente valido.

### Sintassi

```bash
php artisan mod-jobs:fix-dynamic-groups {--customer=<id> : Limita la riparazione a un customer_id} \
                                         {--apply : Applica le modifiche (default: dry-run)}
```

### Esempi

```bash
# Dry-run su tutti i customer
php artisan mod-jobs:fix-dynamic-groups

# Dry-run su un solo customer
php artisan mod-jobs:fix-dynamic-groups --customer=123

# Applica la riparazione
php artisan mod-jobs:fix-dynamic-groups --apply
```

### Comportamento

- Di default è un dry-run: stampa una tabella (`customer_id`, gruppo, chiavi prima, chiavi dopo) senza scrivere nulla.
- Con `--apply`, ogni correzione viene salvata dentro una transazione per riga.

---

## Comandi cron (invocati da script shell esterni)

Questi comandi non si lanciano a mano in produzione: girano via cron esterno attraverso wrapper shell in `html/` (`_emporioApp-*.sh`), come descritto in `CLAUDE.md`. Elencati qui solo per riferimento.

| Comando | Cosa fa |
|---|---|
| `cron:customer:renew` | Rinnova i punti dei clienti. |
| `cron:customer:active-reset` | Resetta il flag attivo/ricezione dei clienti. |
| `cron:report:mailsend` | Invia i report via email. |
| `cron:setting:db2Excel` | Marca un flag file letto da un sidecar Python, che esporta dati su Excel e li invia via email. |
