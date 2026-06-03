#!/usr/bin/env php
<?php
/**
 * Manual Migration Runner
 * Run with: php migrate_manual.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Get database instance
$db = $app->make('db');

try {
    echo "🔄 Starting migration process...\n\n";

    // 1. Drop old tables if they exist
    echo "1️⃣ Dropping old tables...\n";
    $db->statement('DROP TABLE IF EXISTS absenpulang');
    $db->statement('DROP TABLE IF EXISTS absendatang');
    $db->statement('DROP TABLE IF EXISTS absensi');
    echo "✅ Old tables dropped\n\n";

    // 2. Create fresh absensi table
    echo "2️⃣ Creating fresh absensi table...\n";
    $db->statement('
        CREATE TABLE absensi (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            EMAIL VARCHAR(50) NOT NULL,
            TANGGAL DATE NOT NULL,
            
            DATETIME_DATANG DATETIME NULL,
            FOTO_DATANG LONGTEXT NULL,
            LOKASI_DATANG VARCHAR(255) NULL,
            LAT_DATANG DECIMAL(10,8) NULL,
            LNG_DATANG DECIMAL(11,8) NULL,
            
            DATETIME_PULANG DATETIME NULL,
            FOTO_PULANG LONGTEXT NULL,
            LOKASI_PULANG VARCHAR(255) NULL,
            LAT_PULANG DECIMAL(10,8) NULL,
            LNG_PULANG DECIMAL(11,8) NULL,
            
            STATUS ENUM("HADIR","TERLAMBAT","TIDAK_HADIR") DEFAULT "HADIR",
            KOMPENSASI INT DEFAULT 0,
            
            ALASAN_TIDAK_HADIR ENUM("SAKIT","IZIN") NULL,
            SURAT_IZIN LONGTEXT NULL,
            
            ID_CABANG VARCHAR(50) NULL,
            
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            UNIQUE KEY unique_absensi (EMAIL, TANGGAL),
            FOREIGN KEY (EMAIL) REFERENCES karyawan(EMAIL) ON DELETE RESTRICT ON UPDATE RESTRICT,
            FOREIGN KEY (ID_CABANG) REFERENCES cabang(ID_CABANG) ON DELETE SET NULL ON UPDATE CASCADE,
            
            INDEX idx_tanggal (TANGGAL),
            INDEX idx_status (STATUS),
            INDEX idx_email_tanggal (EMAIL, TANGGAL)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');
    echo "✅ Fresh absensi table created\n\n";

    // 3. Update migrations table
    echo "3️⃣ Updating migrations table...\n";
    $db->table('migrations')->where('migration', '2026_06_03_081000_create_absensi_table')->delete();
    $db->table('migrations')->where('migration', '2026_06_03_081100_migrate_absensi_data')->delete();
    $db->table('migrations')->where('migration', '2026_06_03_082400_create_fresh_absensi_table')->delete();
    
    $db->table('migrations')->insert([
        'migration' => '2026_06_03_083000_create_absensi_fresh_final',
        'batch' => $db->table('migrations')->max('batch') + 1
    ]);
    echo "✅ Migrations table updated\n\n";

    echo "🎉 Migration completed successfully!\n";
    echo "✨ Database is ready for the new attendance system\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
