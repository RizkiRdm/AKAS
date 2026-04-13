-- 1. Lookup tables (1NF-3NF)
CREATE TABLE categories (
    id_kat VARCHAR(10) PRIMARY KEY,
    nama_kat VARCHAR(100) NOT NULL
);

CREATE TABLE units (
    id_satuan VARCHAR(10) PRIMARY KEY,
    nama_satuan VARCHAR(50) NOT NULL
);

-- 2. Master Stok
CREATE TABLE products (
    id_brg VARCHAR(20) PRIMARY KEY,
    nama_brg VARCHAR(255) NOT NULL,
    id_kat VARCHAR(10) REFERENCES categories(id_kat),
    id_satuan VARCHAR(10) REFERENCES units(id_satuan),
    stok INT DEFAULT 0 CHECK (stok >= 0),
    harga_beli DECIMAL(15,2),
    harga_jual DECIMAL(15,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Supplier
CREATE TABLE suppliers (
    id_supplier VARCHAR(10) PRIMARY KEY,
    nama_supplier VARCHAR(255) NOT NULL,
    alamat TEXT,
    no_telp VARCHAR(20)
);

-- 4. Users + Roles (merge pegawai + pengguna)
CREATE TABLE users (
    id_user SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nama_pegawai VARCHAR(255),
    role VARCHAR(20) CHECK (role IN ('admin', 'cashier')) NOT NULL,  -- admin / cashier
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Stock In (barang masuk)
CREATE TABLE stock_in (
    id_masuk SERIAL PRIMARY KEY,
    tgl_masuk DATE DEFAULT CURRENT_DATE,
    id_supplier VARCHAR(10) REFERENCES suppliers(id_supplier),
    id_brg VARCHAR(20) REFERENCES products(id_brg),
    jumlah INT CHECK (jumlah > 0),
    total_harga DECIMAL(15,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Sales (transaksi penjualan)
CREATE TABLE sales (
    id_jual SERIAL PRIMARY KEY,
    tgl_jual TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_brg VARCHAR(20) REFERENCES products(id_brg),
    id_user VARCHAR(10) REFERENCES users(id_user),  -- cashier
    id_shift INT REFERENCES shifts(id_shift),       -- link ke shift
    jumlah INT CHECK (jumlah > 0),
    total_bayar DECIMAL(15,2),
    payment_method VARCHAR(50),                     -- cash / qris / ewallet / va
    payment_ref VARCHAR(100),                       -- dari gateway
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 7. Shifts (baru: buat reconciliation)
CREATE TABLE shifts (
    id_shift SERIAL PRIMARY KEY,
    id_user INT REFERENCES users(id_user),
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP,
    starting_float DECIMAL(15,2) DEFAULT 0,   -- modal awal
    ending_cash DECIMAL(15,2),                -- blind count
    variance DECIMAL(15,2) GENERATED ALWAYS AS (ending_cash - (starting_float + calculated_cash_flow)) STORED,
    status VARCHAR(20) DEFAULT 'open' CHECK (status IN ('open','closed'))
);

-- 8. Cash Flow (enhanced)
CREATE TABLE cash_flow (
    id_flow SERIAL PRIMARY KEY,
    id_shift INT REFERENCES shifts(id_shift),
    tgl_flow TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    keterangan TEXT,
    masuk DECIMAL(15,2) DEFAULT 0,
    keluar DECIMAL(15,2) DEFAULT 0
);

-- 9. Audit Log (immutable, baru)
CREATE TABLE audit_log (
    id_log SERIAL PRIMARY KEY,
    table_name VARCHAR(50) NOT NULL,
    record_id BIGINT NOT NULL,
    action VARCHAR(20) NOT NULL,  -- INSERT / UPDATE / DELETE
    old_data JSONB,
    new_data JSONB,
    id_user INT REFERENCES users(id_user),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
