<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // For SQLite (tenant DBs on Dev / Prod usually use SQLite)
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');

            // 1. Update customers table to make email nullable
            if (Schema::hasTable('customers')) {
                DB::statement('
                    CREATE TABLE customers_temp (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        customer_name TEXT NOT NULL,
                        email TEXT UNIQUE,
                        gender TEXT DEFAULT "Male",
                        address TEXT,
                        phone TEXT,
                        created_at DATETIME,
                        updated_at DATETIME
                    )
                ');
                DB::statement('
                    INSERT INTO customers_temp (id, customer_name, email, gender, address, phone, created_at, updated_at)
                    SELECT id, customer_name, email, gender, address, phone, created_at, updated_at FROM customers
                ');
                DB::statement('DROP TABLE customers');
                DB::statement('ALTER TABLE customers_temp RENAME TO customers');
            }

            // 2. Update suppliers table to make email nullable
            if (Schema::hasTable('suppliers')) {
                DB::statement('
                    CREATE TABLE suppliers_temp (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        supplier_name TEXT NOT NULL,
                        email TEXT UNIQUE,
                        gender TEXT,
                        address TEXT,
                        phone TEXT,
                        created_at DATETIME,
                        updated_at DATETIME
                    )
                ');
                DB::statement('
                    INSERT INTO suppliers_temp (id, supplier_name, email, gender, address, phone, created_at, updated_at)
                    SELECT id, supplier_name, email, gender, address, phone, created_at, updated_at FROM suppliers
                ');
                DB::statement('DROP TABLE suppliers');
                DB::statement('ALTER TABLE suppliers_temp RENAME TO suppliers');
            }

            // 3. Update employees table to make email nullable
            if (Schema::hasTable('employees')) {
                DB::statement('
                    CREATE TABLE employees_temp (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        name TEXT NOT NULL,
                        email TEXT,
                        phone TEXT,
                        address TEXT,
                        position TEXT,
                        hire_date DATE,
                        salary DECIMAL(10, 2),
                        status TEXT DEFAULT "Active",
                        created_at DATETIME,
                        updated_at DATETIME
                    )
                ');
                DB::statement('
                    INSERT INTO employees_temp (id, name, email, phone, address, position, hire_date, salary, status, created_at, updated_at)
                    SELECT id, name, email, phone, address, position, hire_date, salary, status, created_at, updated_at FROM employees
                ');
                DB::statement('DROP TABLE employees');
                DB::statement('ALTER TABLE employees_temp RENAME TO employees');
            }

            DB::statement('PRAGMA foreign_keys=ON;');
        } else {
            // For MySQL (if any tenants use MySQL)
            Schema::table('customers', function ($table) {
                $table->string('email')->nullable()->change();
            });
            Schema::table('suppliers', function ($table) {
                $table->string('email')->nullable()->change();
            });
            Schema::table('employees', function ($table) {
                $table->string('email')->nullable()->change();
            });
        }
    }

    public function down()
    {
        // No need to revert since making fields nullable is a non-destructive expansion
    }
};
