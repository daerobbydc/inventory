<?php

namespace Database\Seeders;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\ConsumableTransactionType;
use App\Enums\LoanStatus;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Campus;
use App\Models\Category;
use App\Models\Consumable;
use App\Models\ConsumableTransaction;
use App\Models\LoanItem;
use App\Models\LoanTransaction;
use App\Models\MaintenanceTicket;
use App\Models\Room;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with initial demo data.
     */
    public function run(): void
    {
        // 1. Seed Users for each Role
        $superAdmin = User::create([
            'name' => 'Budi Santoso, S.Kom',
            'email' => 'sarpras@kampus.ac.id',
            'password' => Hash::make('password'),
            'nip_nim' => '198501152010121001',
            'phone' => '081234567890',
            'role' => UserRole::SuperAdmin,
            'department' => 'Biro Administrasi Umum & Sarpras',
            'is_active' => true,
        ]);

        $adminFakultas = User::create([
            'name' => 'Dewi Rahmawati, M.T',
            'email' => 'fakultas.teknik@kampus.ac.id',
            'password' => Hash::make('password'),
            'nip_nim' => '198804222014042002',
            'phone' => '081234567891',
            'role' => UserRole::AdminFakultas,
            'department' => 'Fakultas Teknik & Ilmu Komputer',
            'is_active' => true,
        ]);

        $laboran = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'laboran.komputer@kampus.ac.id',
            'password' => Hash::make('password'),
            'nip_nim' => '199208102018011003',
            'phone' => '081234567892',
            'role' => UserRole::PicRuangan,
            'department' => 'Laboratorium Komputer',
            'is_active' => true,
        ]);

        $teknisi = User::create([
            'name' => 'Hendra Gunawan',
            'email' => 'teknisi@kampus.ac.id',
            'password' => Hash::make('password'),
            'nip_nim' => '199503152020081004',
            'phone' => '081234567893',
            'role' => UserRole::Teknisi,
            'department' => 'Unit Pemeliharaan Sarpras',
            'is_active' => true,
        ]);

        $dosen = User::create([
            'name' => 'Dr. Eng. Irwan Prasetyo',
            'email' => 'dosen@kampus.ac.id',
            'password' => Hash::make('password'),
            'nip_nim' => '198006122005011002',
            'phone' => '081234567894',
            'role' => UserRole::Dosen,
            'department' => 'Program Studi Informatika',
            'is_active' => true,
        ]);

        $mahasiswa = User::create([
            'name' => 'Rian Pratama',
            'email' => 'mahasiswa@kampus.ac.id',
            'password' => Hash::make('password'),
            'nip_nim' => '21010045',
            'phone' => '081234567895',
            'role' => UserRole::Mahasiswa,
            'department' => 'S1 Teknik Informatika',
            'is_active' => true,
        ]);

        // 2. Master Kampus, Gedung & Ruangan
        $campus = Campus::create([
            'code' => 'KMP-01',
            'name' => 'Kampus Terpadu Utama',
            'address' => 'Jl. Pendidikan No. 45, Kampus Pusat',
            'phone' => '(021) 7894561',
        ]);

        $gedungRektorat = Building::create([
            'campus_id' => $campus->id,
            'code' => 'GDG-A',
            'name' => 'Gedung Rektorat & Administrasi',
            'total_floors' => 4,
            'description' => 'Pusat administrasi, pimpinan, dan pelayanan terpadu',
        ]);

        $gedungLab = Building::create([
            'campus_id' => $campus->id,
            'code' => 'GDG-B',
            'name' => 'Gedung Laboratorium Sains & Komputer',
            'total_floors' => 3,
            'description' => 'Laboratorium riset, praktikum komputer, dan multimedia',
        ]);

        $ruangSarpras = Room::create([
            'building_id' => $gedungRektorat->id,
            'code' => 'R-101',
            'name' => 'Kantor Pengelolaan Sarpras',
            'floor_number' => 1,
            'type' => 'office',
            'pic_user_id' => $superAdmin->id,
            'capacity' => 15,
            'description' => 'Pusat operasional inventaris dan gudang logistik internal',
        ]);

        $ruangRapat = Room::create([
            'building_id' => $gedungRektorat->id,
            'code' => 'R-201',
            'name' => 'Ruang Rapat Senat Kampus',
            'floor_number' => 2,
            'type' => 'auditorium',
            'pic_user_id' => $superAdmin->id,
            'capacity' => 60,
            'description' => 'Ruang sidang dan pertemuan resmi pimpinan',
        ]);

        $labJaringan = Room::create([
            'building_id' => $gedungLab->id,
            'code' => 'LAB-101',
            'name' => 'Laboratorium Jaringan & Keamanan Siber',
            'floor_number' => 1,
            'type' => 'lab',
            'pic_user_id' => $laboran->id,
            'capacity' => 35,
            'description' => 'Laboratorium praktikum networking, router, dan server',
        ]);

        $labMultimedia = Room::create([
            'building_id' => $gedungLab->id,
            'code' => 'LAB-202',
            'name' => 'Laboratorium Multimedia & Studio Kreatif',
            'floor_number' => 2,
            'type' => 'lab',
            'pic_user_id' => $laboran->id,
            'capacity' => 30,
            'description' => 'Studio podcast, editing video, dan fotografi',
        ]);

        // 3. Master Kategori & Vendor
        $catHardware = Category::create([
            'code' => 'ELK-PC',
            'name' => 'Komputer & Perangkat Keras',
            'description' => 'PC Dekstop, Laptop, All-in-One, dan Server',
            'is_consumable' => false,
        ]);

        $catAudioVisual = Category::create([
            'code' => 'ELK-AV',
            'name' => 'Audio Visual & Multimedia',
            'description' => 'Proyektor, Kamera, Mic, Sound System',
            'is_consumable' => false,
        ]);

        $catFurniture = Category::create([
            'code' => 'MBL-01',
            'name' => 'Mebel & Furnitur Ruangan',
            'description' => 'Meja praktikum, kursi dosen, lemari arsip',
            'is_consumable' => false,
        ]);

        $catAtk = Category::create([
            'code' => 'ATK-01',
            'name' => 'Alat Tulis Kantor & Kertas',
            'description' => 'Kertas HVS, spidol, pulpen, cartridge printer',
            'is_consumable' => true,
        ]);

        $vendor1 = Vendor::create([
            'name' => 'PT Graha Solusi Informatika',
            'contact_person' => 'Bambang Sudibyo',
            'phone' => '08119876543',
            'email' => 'sales@grahasolusi.com',
            'address' => 'Mangga Dua Square Blok C No. 12, Jakarta',
            'notes' => 'Penyedia resmi PC Dell dan perangkat Cisco',
        ]);

        $vendor2 = Vendor::create([
            'name' => 'CV Cahaya Sarana Nusantara',
            'contact_person' => 'Rina Wulandari',
            'phone' => '08123334455',
            'email' => 'info@cahayasarana.co.id',
            'address' => 'Kawasan Industri Rungkut No. 5, Surabaya',
            'notes' => 'Penyedia proyektor Epson dan mebel kampus berstandar SNI',
        ]);

        // 4. Aset Tetap Contoh
        $asset1 = Asset::create([
            'asset_code' => 'AST/ELK/2025/0001',
            'serial_number' => 'DL-OPT-89211',
            'name' => 'Dell OptiPlex 7000 All-in-One (Core i7, 16GB, 512GB SSD)',
            'brand' => 'Dell',
            'model' => 'OptiPlex 7000 AiO',
            'category_id' => $catHardware->id,
            'room_id' => $labJaringan->id,
            'vendor_id' => $vendor1->id,
            'purchase_date' => '2025-02-15',
            'purchase_cost' => 17500000,
            'useful_life_years' => 4,
            'salvage_value' => 2000000,
            'condition' => AssetCondition::Good,
            'status' => AssetStatus::Available,
            'qr_code_token' => 'QR-AST-0001-'.Str::random(8),
            'specifications' => 'Intel Core i7-12700, 16GB DDR5, 512GB NVMe, Monitor 24 inch FHD IPS',
            'notes' => 'Digunakan untuk PC Master Laboran Lab Jaringan',
        ]);

        $asset2 = Asset::create([
            'asset_code' => 'AST/ELK/2025/0002',
            'serial_number' => 'CISCO-CAT-9200L',
            'name' => 'Cisco Catalyst 9200L 24-Port Gigabit Switch',
            'brand' => 'Cisco Systems',
            'model' => 'C9200L-24T-4G',
            'category_id' => $catHardware->id,
            'room_id' => $labJaringan->id,
            'vendor_id' => $vendor1->id,
            'purchase_date' => '2025-01-20',
            'purchase_cost' => 28000000,
            'useful_life_years' => 5,
            'salvage_value' => 3000000,
            'condition' => AssetCondition::Good,
            'status' => AssetStatus::Available,
            'qr_code_token' => 'QR-AST-0002-'.Str::random(8),
            'specifications' => '24 Port 10/100/1000, 4x 1G SFP Uplinks, Managed Switch',
            'notes' => 'Terpasang di Rack Server Lab Jaringan',
        ]);

        $asset3 = Asset::create([
            'asset_code' => 'AST/AV/2025/0003',
            'serial_number' => 'SNY-A6400-9831',
            'name' => 'Kamera Mirrorless Sony Alpha A6400 Kit 16-50mm',
            'brand' => 'Sony',
            'model' => 'ILCE-6400L',
            'category_id' => $catAudioVisual->id,
            'room_id' => $labMultimedia->id,
            'vendor_id' => $vendor2->id,
            'purchase_date' => '2025-03-10',
            'purchase_cost' => 14200000,
            'useful_life_years' => 3,
            'salvage_value' => 1500000,
            'condition' => AssetCondition::Good,
            'status' => AssetStatus::Borrowed,
            'qr_code_token' => 'QR-AST-0003-'.Str::random(8),
            'specifications' => '24.2 MP APS-C Sensor, 4K Video, Fast Hybrid AF, Lens E PZ 16-50mm',
            'notes' => 'Aset bergerak untuk praktikum fotografi & peminjaman mahasiswa',
        ]);

        $asset4 = Asset::create([
            'asset_code' => 'AST/AV/2024/0004',
            'serial_number' => 'EPS-EB-X500',
            'name' => 'Proyektor Epson EB-X500 3600 Lumens XGA 3LCD',
            'brand' => 'Epson',
            'model' => 'EB-X500',
            'category_id' => $catAudioVisual->id,
            'room_id' => $ruangRapat->id,
            'vendor_id' => $vendor2->id,
            'purchase_date' => '2024-08-15',
            'purchase_cost' => 6800000,
            'useful_life_years' => 3,
            'salvage_value' => 500000,
            'condition' => AssetCondition::LightDamage,
            'status' => AssetStatus::Maintenance,
            'qr_code_token' => 'QR-AST-0004-'.Str::random(8),
            'specifications' => '3600 ANSI Lumens, Kontras 16.000:1, Resolusi XGA 1024x768',
            'notes' => 'Lampu proyektor mulai redup, sedang menunggu pergantian modul lampu',
        ]);

        // 5. Barang Habis Pakai (Consumables)
        $hvs = Consumable::create([
            'item_code' => 'ATK-HVS-A4',
            'name' => 'Kertas HVS A4 80 GSM Sinar Dunia (Rim)',
            'category_id' => $catAtk->id,
            'unit' => 'rim',
            'current_stock' => 45,
            'minimum_stock' => 15,
            'unit_price' => 55000,
            'notes' => 'Kebutuhan cetak laporan skripsi & administrasi kantor',
        ]);

        $spidol = Consumable::create([
            'item_code' => 'ATK-SPD-WHT',
            'name' => 'Spidol Whiteboard Snowman Boardmarker Hitam (Box)',
            'category_id' => $catAtk->id,
            'unit' => 'box',
            'current_stock' => 8,
            'minimum_stock' => 10, // status isLowStock will be true
            'unit_price' => 85000,
            'notes' => 'Stok menipis, perlu re-order untuk ruang perkuliahan',
        ]);

        // Log Transaksi Masuk Stok
        ConsumableTransaction::create([
            'consumable_id' => $hvs->id,
            'user_id' => $superAdmin->id,
            'type' => ConsumableTransactionType::In,
            'quantity' => 50,
            'reference_number' => 'PO/2026/01/008',
            'notes' => 'Penerimaan stok berkala dari rekanan ATK',
        ]);

        ConsumableTransaction::create([
            'consumable_id' => $hvs->id,
            'user_id' => $dosen->id,
            'type' => ConsumableTransactionType::Out,
            'quantity' => 5,
            'reference_number' => 'REQ/INF/2026/03',
            'notes' => 'Permintaan kertas untuk ujian tengah semester',
        ]);

        // 6. Contoh Transaksi Peminjaman (Sony Alpha dipinjam Mahasiswa)
        $loan = LoanTransaction::create([
            'transaction_code' => 'LOAN/202611/0001',
            'borrower_id' => $mahasiswa->id,
            'approver_id' => $laboran->id,
            'loan_date' => now()->toDateString(),
            'expected_return_date' => now()->addDays(3)->toDateString(),
            'status' => LoanStatus::Borrowed,
            'purpose' => 'Liputan dokumentasi kegiatan Seminar Nasional Himpunan Mahasiswa Informatika',
            'approval_notes' => 'Disetujui untuk pemakaian 3 hari, wajib dijaga kebersihan sensor kamera',
        ]);

        LoanItem::create([
            'loan_transaction_id' => $loan->id,
            'asset_id' => $asset3->id,
            'initial_condition' => AssetCondition::Good,
            'notes' => 'Lensa, baterai 2 buah, dan tas kamera lengkap',
        ]);

        // 7. Tiket Pemeliharaan Mendesak (Sesuai Referensi SIM-SARPRAS Enterprise)
        MaintenanceTicket::create([
            'ticket_code' => 'TCK/202611/0001',
            'asset_id' => $asset1->id,
            'reporter_id' => $laboran->id,
            'technician_id' => $teknisi->id,
            'issue_description' => 'AC Lab Jaringan Gedung C Mati Total. Kompresor tidak menyala dan kipas indoor mati mendadak.',
            'priority' => MaintenancePriority::Critical,
            'status' => MaintenanceStatus::InProgress,
            'cost' => 850000,
            'resolution_notes' => 'Teknisi: Pak Sugeng (Vendor Daikin) • Estimasi penanganan tuntas sore ini pukul 17:00 WIB.',
        ]);

        MaintenanceTicket::create([
            'ticket_code' => 'TCK/202611/0002',
            'asset_id' => $asset4->id,
            'reporter_id' => $dosen->id,
            'technician_id' => $teknisi->id,
            'issue_description' => 'Proyektor R.302 Gd. Bersama Buram. Lampu proyektor aus dan tampilan berkedip merah.',
            'priority' => MaintenancePriority::High,
            'status' => MaintenanceStatus::WaitingParts,
            'cost' => 1250000,
            'resolution_notes' => 'Lampu proyektor aus • Status: PO Sparepart SPR-204 telah diajukan ke rekanan.',
        ]);

        MaintenanceTicket::create([
            'ticket_code' => 'TCK/202611/0003',
            'asset_id' => $asset2->id,
            'reporter_id' => $superAdmin->id,
            'technician_id' => $teknisi->id,
            'issue_description' => 'Lift Gedung Rektorat Pemeliharaan. Pemeriksaan getaran kabel seling dan sensor pintu lantai 3.',
            'priority' => MaintenancePriority::Medium,
            'status' => MaintenanceStatus::InProgress,
            'cost' => 2400000,
            'resolution_notes' => 'Selesai 80% • Pemeriksaan kabel seling berkala semesteran dan penggantian pelumas rel.',
        ]);

        MaintenanceTicket::create([
            'ticket_code' => 'TCK/202611/0004',
            'asset_id' => $asset1->id,
            'reporter_id' => $mahasiswa->id,
            'technician_id' => $teknisi->id,
            'issue_description' => 'Pompa Air Asrama Mahasiswa Putra. Debit air berkurang drastis di blok asrama barat.',
            'priority' => MaintenancePriority::High,
            'status' => MaintenanceStatus::InProgress,
            'cost' => 450000,
            'resolution_notes' => 'Sedang Dikerjakan • Pipa distribusi blok B bocor dan sedang disambung ulang oleh Tim Internal Sarpras.',
        ]);
    }
}
