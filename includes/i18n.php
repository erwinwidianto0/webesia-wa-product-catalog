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

/**
 * Get the core translation mapping
 */
function wpwa_get_translation_maps() {
	return [
		// Common Strings
		'Home'                                => 'Beranda',
		'Category'                            => 'Kategori',
		'Category:'                            => 'Kategori:',
		'Categories'                          => 'Kategori',
		'All Categories'                      => 'Semua Kategori',
		'SKU:'                                => 'SKU:',
		'Read More'                           => 'Selengkapnya',
		'Ringkasan Toko (WA WebEsia Catalog)' => 'Ringkasan Toko (WA WebEsia Catalog)',
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
		'Short Description / Excerpt'         => 'Kutipan / Deskripsi Singkat',
		'Display a product summary to attract buyers on the catalog page.' => 'Tampilkan ringkasan produk untuk memikat pembeli di halaman katalog.',
		'Example: Premium industrial tank with optimized capacity for your business needs...' => 'Contoh: Tangki industri premium dengan kapasitas optimal untuk kebutuhan bisnis Anda...',
		'Excerpt is a concise operational text that will appear in the product card.' => 'Kutipan adalah teks ringkas operasional yang akan tampil di kartu produk.',
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
		'Tab Title (e.g. Features)'           => 'Judul Tab (misal: Fitur)',
		'Remove Tab'                          => 'Hapus Tab',
		'Add New Tab'                         => 'Tambah Tab Baru',
		'Tab Title'                           => 'Judul Tab',
		'Are you sure you want to remove this tab?' => 'Apakah Anda yakin ingin menghapus tab ini?',
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
		'ESTIMATED TOTAL:'                    => 'TOTAL ESTIMASI:',
		'WhatsApp Number'                     => 'Nomor WhatsApp',
		'628xxxxxxxxxx'                       => '08xxxxxxxxxx',
		'Shipping Address'                    => 'Alamat Pengiriman',
		'Street Name, City, Postal Code'      => 'Nama Jalan, Kota, Kode Pos',
		'Note'                                => 'Catatan',
		'Delivery Date, Color, etc'           => 'Tanggal Pengiriman, Warna, dll',
		'Send Order via WhatsApp'             => 'Kirim Pesanan via WhatsApp',
		'Order via WhatsApp'                  => 'Pesan via WhatsApp',
		'Please fill out the form below to order.' => 'Silahkan isi form di bawah untuk memesan.',
		'Buy via WhatsApp'                    => 'Beli via WhatsApp',
		'Hello, I would like to order "%1$s" (ID: %2$d).' => 'Halo Admin, saya ingin pesan "%1$s" (ID: %2$d).',
		"Hello Admin, I would like to order \"{product_name}\" with URL \"{product_url}\":\n\nQuantity: {qty}\nTotal: {total}\nName: {customer_name}\nPhone: {customer_phone}\nAddress: {address}\nNote: {note}\n\nThank you." => "Halo Admin, saya ingin pesan \"{product_name}\" dengan URL \"{product_url}\":\n\nJumlah: {qty}\nTotal: {total}\nNama: {customer_name}\nTelepon: {customer_phone}\nAlamat: {address}\nCatatan: {note}\n\nTerima kasih.",
		'Additional Details:'                 => 'Detail Tambahan:',

		// Products / Catalog
		'No products found.'                  => 'Produk tidak ditemukan.',
		'Product Filter'                      => 'Filter Produk',
		'Reset Filter'                        => 'Reset Filter',
		'No products found with those criteria.' => 'Tidak ada produk yang ditemukan dengan kriteria tersebut.',
		'« Previous'                          => '« Sebelumnya',
		'Next »'                              => 'Selanjutnya »',
		'Price Range'                         => 'Rentang Harga',
		'Min Price'                           => 'Harga Min',
		'Max Price'                           => 'Harga Max',
		'Sort By'                             => 'Urutkan Berdasarkan',
		'Latest'                              => 'Terbaru',
		'Price: Low to High'                  => 'Harga: Rendah ke Tinggi',
		'Price: High to Low'                  => 'Harga: Tinggi ke Rendah',
		'Apply Filter'                        => 'Terapkan Filter',
		'Clear All'                           => 'Hentikan Semua',
		'SALE'                                => 'PROMO',
		'View Details'                        => 'Lihat Detail',
		'Shop'                                => 'Toko',

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
		'Email Address'                       => 'Alamat Email',
		'Write your experience using this product...' => 'Tuliskan pengalaman Bapak menggunakan produk ini...',
		'%s ago'                              => '%s yang lalu',
		'Reviews (%d)'                        => 'Ulasan (%d)',
		'%d Reviews'                          => '%d Ulasan',
		'Based on %s review'                  => 'Berdasarkan %s ulasan',
		'Based on %s reviews'                 => 'Berdasarkan %s ulasan',
		
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
		'Processing...'                       => 'Memproses...',
		'An error occurred. Please try again.' => 'Terjadi kesalahan. Silakan coba lagi.',
		'WA WebEsia Catalog'                  => 'Katalog WA WebEsia',
		'Plugin Language'                     => 'Bahasa Plugin',
		'English (Default)'                   => 'Inggris (Bawaan)',
		'Bahasa Indonesia (Bundled)'          => 'Bahasa Indonesia (Internal)',
		'Custom Translate'                    => 'Custom Translate',
		'Choose the primary language for your catalog.' => 'Pilih bahasa utama untuk katalog Bapak.',
		'Custom Translations'                 => 'Terjemahan Kustom',
		'Search keywords...'                  => 'Cari kata kunci...',
		'Translate or modify any string below. Leave empty to use the original English text.' => 'Terjemahkan atau ubah teks di bawah ini. Kosongkan untuk menggunakan teks asli Bahasa Inggris.',
		'Original String (English)'           => 'Teks Asli (Inggris)',
		'Your Translation'                    => 'Terjemahan Bapak',

		// Post Type & Taxonomy Labels
		'New Product'                         => 'Produk Baru',
		'Edit Product'                        => 'Edit Produk',
		'View Product'                        => 'Lihat Produk',
		'All Products'                        => 'Semua Produk',
		'Search Products'                     => 'Cari Produk',
		'Parent Products:'                    => 'Produk Induk:',
		'No products found in Trash.'         => 'Produk tidak ditemukan di Tempat Sampah.',
		'Main Image'                          => 'Gambar Utama',
		'Set Main Image'                      => 'Atur Gambar Utama',
		'Remove Main Image'                   => 'Hapus Gambar Utama',
		'Use as Main Image'                   => 'Gunakan sebagai Gambar Utama',
		'Search Categories'                   => 'Cari Kategori',
		'Popular Categories'                  => 'Kategori Populer',
		'Parent Category'                     => 'Kategori Induk',
		'Parent Category:'                    => 'Kategori Induk:',
		'Edit Category'                       => 'Edit Kategori',
		'Update Category'                     => 'Perbarui Kategori',
		'Add New Category'                    => 'Tambah Kategori Baru',
		'New Category Name'                   => 'Nama Kategori Baru',
		'Separate categories with commas'     => 'Pisahkan kategori dengan koma',
		'Add or remove categories'            => 'Tambah atau hapus kategori',
		'Choose from the most used categories' => 'Pilih dari kategori yang paling sering digunakan',
		'Gallery saved successfully'          => 'Galeri berhasil disimpan',
		'Product updated. <a href="%s">View product</a>' => 'Produk diperbarui. <a href="%s">Lihat produk</a>',
		'Custom field updated.'               => 'Field kustom diperbarui.',
		'Custom field deleted.'               => 'Field kustom dihapus.',
		'Product restored to revision from %s' => 'Produk dikembalikan ke revisi dari %s',
		'Product published. <a href="%s">View product</a>' => 'Produk diterbitkan. <a href="%s">Lihat produk</a>',
		'Product saved.'                      => 'Produk disimpan.',
		'Product submitted. <a target="_blank" href="%s">Preview product</a>' => 'Produk dikirim. <a target="_blank" href="%s">Pratinjau produk</a>',
		'Product scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview product</a>' => 'Produk dijadwalkal: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Pratinjau produk</a>',
		'Product draft updated. <a target="_blank" href="%s">Preview product</a>' => 'Draf produk diperbarui. <a target="_blank" href="%s">Pratinjau produk</a>',
		'M j, Y @ G:i'                         => 'j M Y @ G:i',

		// Reports & Dashboard
		'Sales Reports'                       => 'Laporan Penjualan',
		'All'                                 => 'Semua',
		'Daily'                               => 'Harian',
		'Weekly'                              => 'Mingguan',
		'Monthly'                             => 'Bulanan',
		'Yearly'                              => 'Tahunan',
		'Export CSV'                          => 'Ekspor CSV',
		'Order Status'                        => 'Status Pesanan',
		'Sales Trend (Last 6 Months)'         => 'Tren Penjualan (6 Bulan Terakhir)',
		'Sales Trend (Today)'                 => 'Tren Penjualan (Hari Ini)',
		'Sales Trend (Daily)'                 => 'Tren Penjualan (Harian)',
		'Sales Trend (Monthly)'               => 'Tren Penjualan (Bulanan)',
		'Sales Trend (Weekly)'                => 'Tren Penjualan (Mingguan)',
		'units sold'                          => 'unit terjual',
		'No product transactions yet.'        => 'Belum ada transaksi produk.',
		'No data available yet.'              => 'Belum ada data tersedia.',

		// Orders
		'Mark as Completed'                   => 'Tandai Selesai',
		'Mark as Failed'                      => 'Tandai Gagal',
		'Order updated.'                      => 'Pesanan diperbarui.',
		'Failed to save order to database.'   => 'Gagal menyimpan pesanan ke database.',
		'Additional Details:'                 => 'Detail Tambahan:',
		'%d order updated.'                   => '%d pesanan diperbarui.',
		'%d orders updated.'                  => '%d pesanan diperbarui.',
	];
}

function wpwa_dynamic_translation_id( $translated, $text, $domain ) {
	if ( 'webesia-wa-product-catalog' !== $domain ) {
		return $translated;
	}

	$lang_mode = get_option( 'wpwa_plugin_language', 'en' );
	
	if ( 'en' === $lang_mode ) {
		return $translated;
	}

	if ( 'custom' === $lang_mode ) {
		$custom_maps = get_option( 'wpwa_custom_translations', [] );
		if ( isset( $custom_maps[$text] ) && ! empty( $custom_maps[$text] ) ) {
			return $custom_maps[$text];
		}
		return $translated;
	}

	$translations = [];

	if ( 'id' === $lang_mode ) {
		$translations = wpwa_get_translation_maps();
	} else {
		// Old behavior: follow WP locale
		$locale = get_locale();
		if ( strpos( $locale, 'id' ) === 0 ) {
			$translations = wpwa_get_translation_maps();
		} else {
			return $translated;
		}
	}

	if ( isset( $translations[$text] ) ) {
		return $translations[$text];
	}

	return $translated;
}

function wpwa_dynamic_translation_plural_id( $translated, $single, $plural, $number, $domain ) {
	if ( 'webesia-wa-product-catalog' !== $domain ) {
		return $translated;
	}

	// Just use the singular logic for map lookup
	$translated_single = wpwa_dynamic_translation_id( $single, $single, $domain );
	
	if ( $translated_single !== $single ) {
		return $translated_single;
	}

	return $translated;
}

function wpwa_dynamic_translation_context_id( $translated, $text, $context, $domain ) {
	if ( 'webesia-wa-product-catalog' !== $domain ) {
		return $translated;
	}

	// Just reuse the main translation map
	return wpwa_dynamic_translation_id( $translated, $text, $domain );
}

/**
 * Get Currency Symbol/Code
 */
function wpwa_get_currency() {
	return get_option( 'wpwa_currency', '$' );
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
