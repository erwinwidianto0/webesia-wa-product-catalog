<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dynamic Translation Flow for Indonesian Users
 * Provides built-in translations for the 'webesia-wa-product-catalog' domain without requiring .mo files.
 */
add_filter( 'gettext', 'wpwa_dynamic_translation_id', 10, 3 );
add_filter( 'ngettext', 'wpwa_dynamic_translation_plural_id', 10, 5 );
add_filter( 'gettext_with_context', 'wpwa_dynamic_translation_context_id', 10, 4 );

function wpwa_dynamic_translation_id( $translated, $text, $domain ) {
	if ( 'webesia-wa-product-catalog' !== $domain ) {
		return $translated;
	}

	$locale = get_locale();
	if ( strpos( $locale, 'id' ) !== 0 ) {
		return $translated;
	}

	$translations = [
		// Common Strings
		'Home'                                => 'Beranda',
		'Category:'                            => 'Kategori:',
		'Categories'                          => 'Kategori',
		'SKU:'                                => 'SKU:',
		'Read More'                           => 'Selengkapnya',
		'Ringkasan Toko (WebEsia Product Catalog)' => 'Ringkasan Toko (WebEsia Product Catalog)',
		'Total Produk'                        => 'Total Produk',
		'Pesanan Selesai'                     => 'Pesanan Selesai',
		'Akses Cepat:'                        => 'Akses Cepat:',
		'Tambah Produk'                       => 'Tambah Produk',
		'Daftar Pesanan'                      => 'Daftar Pesanan',
		'Laporan'                             => 'Laporan',
		'Pengaturan'                          => 'Pengaturan',
		'Products'                            => 'Produk',
		'Product'                             => 'Produk',
		'Add New'                             => 'Tambah Baru',
		'Add New Product'                     => 'Tambah Produk Baru',
		'Product Short Description'           => 'Deskripsi Singkat Produk',
		'Product Short Description (Excerpt)' => 'Kutipan / Deskripsi Singkat Produk',
		'Kutipan / Deskripsi Singkat'         => 'Kutipan / Deskripsi Singkat',
		'Tampilkan ringkasan produk untuk memikat pembeli di halaman katalog.' => 'Tampilkan ringkasan produk untuk memikat pembeli di halaman katalog.',
		'Contoh: Layanan profesional terintegrasi untuk meningkatkan performa bisnis Anda...' => 'Contoh: Layanan profesional terintegrasi untuk meningkatkan performa bisnis Anda...',
		'Kutipan adalah teks ringkas operasional yang akan tampil di kartu produk.' => 'Kutipan adalah teks ringkas operasional yang akan tampil di kartu produk.',
		'Tuliskan deskripsi singkat yang menarik untuk pelanggan.' => 'Tuliskan deskripsi singkat yang menarik untuk pelanggan.',
		'WA Products'                         => 'Produk WA',
		'Order List'                          => 'Daftar Pesanan',
		'Reports'                             => 'Laporan',
		'Product Details'                     => 'Detail Produk',
		'Product Gallery'                     => 'Galeri Produk',
		'Custom Product Tabs'                 => 'Tab Produk Kustom',
		'Add product gallery images'          => 'Tambah gambar galeri produk',
		'Gallery saved'                       => 'Galeri disimpan',
		'Delete image'                        => 'Hapus gambar',
		'Featured Image'                      => 'Gambar Utama',
		'Set featured image'                  => 'Atur Gambar Utama',
		'Remove featured image'               => 'Hapus Gambar Utama',
		'Tambah Tab Baru'                     => 'Tambah Tab Baru',
		'Hapus Tab'                           => 'Hapus Tab',
		'Judul Tab'                           => 'Judul Tab',
		'Judul Tab (e.g. Fitur)'              => 'Judul Tab (misal: Fitur)',
		'Tab baru berhasil ditambahkan.'      => 'Tab baru berhasil ditambahkan.',
		
		// Modal / Order Form
		'Order Details'                       => 'Detail Pesanan',
		'Quantity'                            => 'Jumlah',
		'Your Name'                           => 'Nama Anda',
		'John Doe'                            => 'Budi Santoso',
		'Total Estimate:'                     => 'Total Estimasi:',
		'WhatsApp Number'                     => 'Nomor WhatsApp',
		'Shipping Address'                    => 'Alamat Pengiriman',
		'Street, City, Zip Code'              => 'Nama Jalan, Kota, Kode Pos',
		'Note'                                => 'Catatan',
		'Start date, color preference, etc.'  => 'Tanggal pengiriman, warna, dll.',
		'Send Order via WhatsApp'             => 'Kirim Pesanan via WhatsApp',
		'Order via WhatsApp'                  => 'Pesan via WhatsApp',
		'Beli via WhatsApp'                   => 'Beli via WhatsApp',

		// Products / Catalog
		'No products found.'                  => 'Produk tidak ditemukan.',
		'Product Filter'                      => 'Filter Produk',
		'Reset Filter'                        => 'Reset Filter',
		'No products found with those criteria.' => 'Tidak ada produk yang ditemukan dengan kriteria tersebut.',
		'« Previous'                          => '« Sebelumnya',
		'Next »'                              => 'Selanjutnya »',

		// Reviews
		'Average Rating'                      => 'Rating Rata-rata',
		'Regular Price (IDR):'                => 'Harga Normal (Rp):',
		'Sale Price (Optional):'              => 'Harga Diskon (Opsional):',
		'Stock (Optional):'                   => 'Stok (Opsional):',
		'Unlimited'                           => 'Tanpa Batas',
		'Minimal Order:'                      => 'Minimal Order:',
		'WhatsApp Number (Override):'         => 'Nomor WhatsApp (Khusus):',
		'Leave empty for global number (e.g. 628xxx)' => 'Kosongkan jika ingin menggunakan nomor utama (contoh: 628xxx)',
		'Fill this if you want orders for this product to go to a different WhatsApp number.' => 'Isi bagian ini jika Bapak ingin pesanan produk ini masuk ke nomor WhatsApp yang berbeda.',
		'Product is Active (Display in Catalog)' => 'Produk Aktif (Tampilkan di Katalog)',
		'Your Rating'                         => 'Rating Anda',
		'Reviews'                             => 'Ulasan',
		'Review'                              => 'Ulasan',
		'Submit Review'                       => 'Kirim Ulasan',
		'Write Your Review'                   => 'Tulis Ulasan Anda',
		'Reply to %s'                         => 'Balas ke %s',
		'No reviews yet. Be the first to review!' => 'Belum ada ulasan. Jadilah yang pertama memberikan ulasan!',
		'Name'                                => 'Nama',
		'Email'                               => 'Email',
		'%s ago'                              => '%s yang lalu',
		
		// Settings / Admin
		'WhatsApp Settings'                   => 'Pengaturan WhatsApp',
		'Settings'                            => 'Pengaturan',
		'WhatsApp Order Settings'             => 'Pengaturan Pesanan WhatsApp',
		'Save Settings'                       => 'Simpan Pengaturan',
		'Order List'                          => 'Daftar Pesanan',
		'Orders'                              => 'Pesanan',
		'Orders via WhatsApp'                 => 'Pesanan via WhatsApp',
		'Order #'                             => 'No. Pesanan',
		'Customer'                            => 'Pelanggan',
		'Product'                             => 'Produk',
		'Total'                               => 'Total',
		'Status'                              => 'Status',
		'Date'                                => 'Tanggal',
		'Actions'                             => 'Aksi',
		'Address'                             => 'Alamat',
		'Note'                                => 'Catatan',
		'Complete'                            => 'Selesai',
		'Failed'                              => 'Gagal',
		'Delete'                              => 'Hapus',
		'Total Revenue'                       => 'Total Omzet',
		'from last month'                     => 'dari bulan lalu',
		'No previous data'                    => 'Data bulan lalu kosong',
		'Completed Orders'                    => 'Pesanan Selesai',
		'Pending Orders'                      => 'Pesanan Menunggu',
		'Need attention'                      => 'Butuh perhatian',
		'Top Product'                         => 'Produk Terlaris',
		'items sold'                          => 'unit terjual',
		'Pending'                             => 'Menunggu',
		'Completed'                           => 'Selesai',
		'Are you sure?'                       => 'Apakah Anda yakin?',
		'Settings saved and permalinks updated.' => 'Pengaturan disimpan dan permalink diperbarui.',
		'Auto-setup completed. "Toko" page has been verified/created.' => 'Setup otomatis selesai. Halaman "Toko" telah diverifikasi/dibuat.',
		'Select Page'                         => 'Pilih Halaman',
		'Language Updates'                    => 'Pembaruan Bahasa',
		'Current Language:'                   => 'Bahasa Saat Ini:',
		'Translations are handled automatically for Indonesian. For other languages, use Loco Translate.' => 'Terjemahan bahasa Indonesia ditangani secara otomatis. Untuk bahasa lain, gunakan Loco Translate.',
		'Cek Pembaruan Bahasa'                => 'Cek Pembaruan Bahasa',
		'Semua bahasa sudah versi terbaru.'   => 'Semua bahasa sudah versi terbaru.',
		'Laporan Penjualan'                   => 'Laporan Penjualan',
		'Total Omzet Keseluruhan'             => 'Total Omzet Keseluruhan',
		'pertumbuhan dibanding bulan lalu'    => 'pertumbuhan dibanding bulan lalu',
		'Belum ada data pembanding bulan lalu' => 'Belum ada data pembanding bulan lalu',
		'Bulan Ini'                           => 'Bulan Ini',
		'Bulan Lalu'                          => 'Bulan Lalu',
		'Ringkasan Pesanan'                   => 'Ringkasan Pesanan',
		'Total Pesanan Masuk'                  => 'Total Pesanan Masuk',
		'Produk Terlaris (Top 5)'             => 'Produk Terlaris (Top 5)',
		'Nama Produk'                         => 'Nama Produk',
		'Unit Terjual'                        => 'Unit Terjual',
		'Estimasi Omzet'                      => 'Estimasi Omzet',
		'Belum ada data produk.'              => 'Belum ada data produk.',
		'Reports'                             => 'Laporan',
		'Tren Penjualan (Minggu Ini)'         => 'Tren Penjualan (Minggu Ini)',
		'Tren Penjualan (Harian)'            => 'Tren Penjualan (Harian)',
		'Tren Penjualan (Mingguan)'           => 'Tren Penjualan (Mingguan)',
		'Tren Penjualan (Bulanan)'            => 'Tren Penjualan (Bulanan)',
		'Tren Penjualan (Hari Ini)'           => 'Tren Penjualan (Hari Ini)',
		'Harian'                              => 'Harian',
		'Semua'                               => 'Semua',
		'Tren Penjualan (6 Bulan Terakhir)'   => 'Tren Penjualan (6 Bulan Terakhir)',
		'Produk Unggulan'                     => 'Produk Unggulan',
		'Custom'                              => 'Custom Tanggal',
		'Filter'                              => 'Filter',
		'Dari'                                => 'Dari',
		'Sampai'                              => 'Sampai',
		'Export Excel'                        => 'Export Excel',
		'WhatsApp Number (e.g. 628xxx)'       => 'Nomor WhatsApp (misal: 628xxx)',
		'Shop Page (Catalog)'                 => 'Halaman Toko (Katalog)',
		'Select a page to display your product catalog.' => 'Pilih halaman untuk menampilkan katalog produk Anda.',
		'Single Product Slug (Permalinks)'    => 'Slug Produk Tunggal (Permalink)',
		'Default: %s. (Example: domain.com/produk/bag-name)' => 'Bawaan: %s. (Contoh: domain.com/produk/nama-barang)',
		'Archive Catalog Slug'                => 'Slug Arsip Katalog',
		'Default: %s. (Example: domain.com/toko/)' => 'Bawaan: %s. (Contoh: domain.com/toko/)',
		'Buy Button Text'                     => 'Teks Tombol Beli',
		'Popup Welcome Message'               => 'Pesan Selamat Datang Popup',
		'WhatsApp Message Template'           => 'Template Pesan WhatsApp',
		'Available Tags:'                     => 'Tag yang tersedia:',
		'Tips:'                               => 'Tips:',
		'Use {details} to automatically list all fields from the "Order Form" tab. If you don\'t use {details}, custom fields will be appended at the end of the message.' => 'Gunakan {details} untuk mencantumkan semua field dari tab "Formulir Order" secara otomatis. Jika Bapak tidak menggunakan {details}, field kustom akan ditambahkan di akhir pesan.',
		'pertumbuhan dibanding bulan lalu'    => 'pertumbuhan dibanding bulan lalu',
		'Umum'                                => 'Umum',
		'Formulir Order'                      => 'Formulir Order',
		'Bahasa'                              => 'Bahasa',
		'Aktif'                               => 'Aktif',
		'Wajib'                               => 'Wajib',
		'Tipe'                                => 'Tipe',
		'Tambah Field Baru'                   => 'Tambah Field Baru',
		'Aktifkan, ubah label, atau atur field wajib untuk formulir order WhatsApp. Bapak juga bisa menambah field baru sesuai kebutuhan.' => 'Aktifkan, ubah label, atau atur field wajib untuk formulir order WhatsApp. Bapak juga bisa menambah field baru sesuai kebutuhan.',
		'Alternative:'                        => 'Alternatif:',
		'You can also use the shortcode'      => 'Bapak juga bisa menggunakan shortcode',
		'in Elementor, Gutenberg, or any page builder.' => 'di Elementor, Gutenberg, atau page builder apapun.',
	];

	if ( isset( $translations[$text] ) ) {
		return $translations[$text];
	}

	return $translated;
}

function wpwa_dynamic_translation_plural_id( $translated, $single, $plural, $number, $domain ) {
	if ( 'webesia-wa-product-catalog' !== $domain ) {
		return $translated;
	}

	$locale = get_locale();
	if ( strpos( $locale, 'id' ) !== 0 ) {
		return $translated;
	}

	// In Indonesian, there is no plural grammatic change (just repeat the word or use context)
	$translations = [
		'Based on %s review'  => 'Berdasarkan %s ulasan',
		'Based on %s reviews' => 'Berdasarkan %s ulasan',
		'Ulasan (%d)'         => 'Ulasan (%d)',
		'Reviews (%d)'        => 'Ulasan (%d)',
		'%d Reviews'          => '%d Ulasan',
		'%d Ulasan'           => '%d Ulasan',
	];

	if ( isset( $translations[$single] ) ) {
		return $translations[$single];
	}
	
	if ( isset( $translations[$plural] ) ) {
		return $translations[$plural];
	}

	return $translated;
}

function wpwa_dynamic_translation_context_id( $translated, $text, $context, $domain ) {
	if ( 'webesia-wa-product-catalog' !== $domain ) {
		return $translated;
	}

	$locale = get_locale();
	if ( strpos( $locale, 'id' ) !== 0 ) {
		return $translated;
	}

	// Just reuse the main translation map for simplicity
	return wpwa_dynamic_translation_id( $translated, $text, $domain );
}

/**
 * Get Currency Symbol/Code
 */
function wpwa_get_currency() {
	return get_option( 'wpwa_currency', 'Rp' );
}

/**
 * Format Price with Currency
 */
function wpwa_format_price( $price ) {
	$currency = wpwa_get_currency();
	$price = floatval( $price );
	
	// Format: Rp 1.000.000 or USD 1,000.00
	if ( strtoupper( $currency ) === 'RP' || strtoupper( $currency ) === 'IDR' ) {
		return 'Rp ' . number_format( $price, 0, ',', '.' );
	} else {
		return $currency . ' ' . number_format( $price, 2 );
	}
}
