# ProyekRPL - Petunjuk Instalasi

Berikut adalah langkah-langkah untuk menjalankan sistem ini di lingkungan lokal Anda.

## Prasyarat

-   PHP >= 8.1
-   Composer
-   Visual Studio Code (Sebagai Text editor)
-   Node.js & npm
-   Database (MySQL, SQLite, dsb.)
-   Laragon/XAMPP/WAMP (opsional, untuk Windows)

## Langkah Instalasi

untuk Langkah langkah ini dilakukan melalui Terminal vscode

1. **Clone Repository**

    ```bash
    git clone https://github.com/itshzlnust/Proyek-RPL.git
    cd Proyek-RPL
    ```

2. **Install Dependencies**

```bash
  composer install
```

3. **Salin File Environment**

```bash
  copy .env.example .env
```

4. **Generate Application Key**

```bash
  php artisan key:generate
```

5. **Konfigurasi Database**

    Edit file .env dan sesuaikan konfigurasi database sesuai kebutuhan Anda.

6. **Migrasi Database**

```bash
  php artisan migrate
```

7. **Jalankan Server Lokal**
    ```bash
     php artisan serve
    ```
8. **Testing**
  ```bash
     php artisan test
  ```
