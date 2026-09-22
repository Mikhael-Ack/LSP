// E2E Test Suite for Dapur Ina Aina - Restoran POS & Operations Management System
// Utilizing Playwright headless browser from E:/gurumusik/gurumusikcore

let chromium;
try {
    chromium = require('playwright').chromium;
} catch (e) {
    try {
        chromium = require('E:/gurumusik/gurumusikcore/gurumusik-frontend/.playwright-local/node_modules/playwright').chromium;
    } catch (e2) {
        console.error('Playwright tidak ditemukan. Silakan jalankan: npm install -D playwright');
        process.exit(1);
    }
}

(async () => {
    console.log('🚀 [E2E PLAYWRIGHT] Memulai pengujian otomatis Sistem Dapur Ina Aina...');
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    const page = await context.newPage();

    page.on('console', msg => console.log('  [BROWSER CONSOLE]', msg.type(), msg.text()));
    page.on('pageerror', err => console.log('  [BROWSER ERROR]', err.message));

    let passedTests = 0;
    let failedTests = 0;

    function assert(condition, message) {
        if (condition) {
            console.log(`  ✅ PASS: ${message}`);
            passedTests++;
        } else {
            console.error(`  ❌ FAIL: ${message}`);
            failedTests++;
        }
    }

    try {
        // ==========================================
        // TEST 1: LANDING PAGE & NAVIGATION
        // ==========================================
        console.log('\n--- [TEST 1] Pengujian Landing Page & Akses Publik ---');
        await page.goto('http://127.0.0.1:8000/', { waitUntil: 'domcontentloaded' });
        const title = await page.title();
        assert(title.includes('Dapur Ina Aina'), `Title memuat nama restoran (Aktual: "${title}")`);

        // Check if workstation simulation exists
        const posSection = await page.$('#simulasi-pos');
        assert(posSection !== null, 'Section interaktif simulasi POS tablet workstation terpasang');

        // Navigate to Login Page
        await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'domcontentloaded' });
        const loginHeader = await page.textContent('h2');
        assert(loginHeader.includes('Login Pengguna'), 'Halaman login dapat diakses dengan benar');

        // ==========================================
        // TEST 2A: KASIR ROLE - ORDER 1 (PEMBAYARAN TUNAI)
        // ==========================================
        console.log('\n--- [TEST 2A] Pengujian Kasir: Pesanan & Pelunasan TUNAI (Cash) ---');
        await page.fill('input[name="username"]', 'kasir');
        await page.fill('input[name="password"]', 'kasir123');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/kasir**', { timeout: 10000 });

        assert(page.url().includes('/kasir'), `Kasir berhasil login dan dialihkan ke POS (URL: ${page.url()})`);

        // [TEST VALIDASI 1: Keranjang Kosong]
        console.log('  ℹ️ Menguji validasi POS: Terbitkan billing saat keranjang kosong...');
        await page.click('#btn-submit-order');
        await page.waitForSelector('#modal-validasi:not(.hidden)', { timeout: 3000 });
        const valTitle1 = await page.textContent('#modal-validasi-title');
        const valMsg1 = await page.textContent('#modal-validasi-msg');
        assert(valTitle1.includes('Menu Belum Dipilih'), 'Modal validasi muncul: Menu Belum Dipilih');
        assert(valMsg1.includes('Harap memilih Menu'), 'Pesan peringatan memilih menu tampil dengan tepat');
        await page.click('#btn-tutup-validasi');
        await page.waitForTimeout(300);

        // Test POS: Click product card directly to add to cart
        const firstMenuCard = await page.$('.menu-item:not(.cursor-not-allowed)');
        assert(firstMenuCard !== null, 'Item menu katalog ditemukan di POS');

        const firstMenuName = await firstMenuCard.$eval('h3', el => el.textContent.trim());
        console.log(`  ℹ️ Kasir memilih menu: "${firstMenuName}"`);
        await firstMenuCard.click();
        await page.waitForTimeout(400);

        // [TEST VALIDASI 2: Nomor Meja Kosong]
        console.log('  ℹ️ Menguji validasi POS: Terbitkan billing saat menu ada tapi nomor meja kosong...');
        await page.click('#btn-submit-order');
        await page.waitForSelector('#modal-validasi:not(.hidden)', { timeout: 3000 });
        const valTitle2 = await page.textContent('#modal-validasi-title');
        const valMsg2 = await page.textContent('#modal-validasi-msg');
        assert(valTitle2.includes('Nomor Meja Kosong'), 'Modal validasi muncul: Nomor Meja Kosong');
        assert(valMsg2.includes('Harap mengisi Nomor Meja'), 'Pesan peringatan nomor meja tampil dengan tepat');
        await page.click('#btn-tutup-validasi');
        await page.waitForTimeout(300);

        // Click a second time to increment quantity
        await firstMenuCard.click();
        await page.waitForTimeout(400);
        const subtotalText = await page.textContent('#subtotal-val');
        assert(subtotalText !== 'Rp 0', `Subtotal pesanan terhitung otomatis: ${subtotalText}`);

        // Enter Table Number and Submit Order
        await page.fill('#no_meja', 'Meja 05');
        console.log('  ℹ️ Kasir mengisi Meja 05 dan menerbitkan tagihan...');
        await page.click('#btn-submit-order');
        await page.waitForURL('**/kasir/billing/**', { timeout: 10000 });

        assert(page.url().includes('/kasir/billing/'), `Billing 1 berhasil diterbitkan (URL: ${page.url()})`);

        // Complete Payment as Cash
        console.log('  ℹ️ Kasir menyelesaikan pembayaran Metode 1 (Tunai)...');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
            page.click('button:has-text("Selesaikan Pembayaran Tunai")')
        ]);

        const bodyPaidTunai = await page.textContent('body');
        assert(bodyPaidTunai.includes('LUNAS') || bodyPaidTunai.includes('PAID'), 'Status transaksi Tunai berhasil menjadi LUNAS');
        assert(bodyPaidTunai.includes('Tunai (Cash)') || bodyPaidTunai.includes('Tunai'), 'Struk menampilkan rincian metode pembayaran Tunai');

        // ==========================================
        // TEST 2B: KASIR ROLE - ORDER 2 (PEMBAYARAN NON-TUNAI MIDTRANS SANDBOX)
        // ==========================================
        console.log('\n--- [TEST 2B] Pengujian Kasir: Pesanan & Pelunasan NON-TUNAI (Midtrans Sandbox Simulator) ---');
        await page.goto('http://127.0.0.1:8000/kasir', { waitUntil: 'domcontentloaded' });
        
        // Add item to cart
        const secondMenuCard = await page.$('.menu-item:not(.cursor-not-allowed)');
        await secondMenuCard.click();
        await page.waitForTimeout(400);

        // Fill table 08
        await page.fill('#no_meja', 'Meja 08');
        console.log('  ℹ️ Kasir membuat pesanan Meja 08 untuk Non-Tunai Midtrans...');
        await page.click('#btn-submit-order');
        await page.waitForURL('**/kasir/billing/**', { timeout: 25000 });

        // Select Non-Tunai (Langsung memicu pop-up resmi Midtrans Snap otomatis)
        console.log('  ℹ️ Kasir memilih opsi pembayaran 2. Non-Tunai (Auto Pop-up Snap)...');
        await page.click('label:has-text("2. Non-Tunai")');
        await page.waitForTimeout(1000);

        // Verify Non-Tunai panel is visible
        const nonTunaiPanel = await page.$('#field-nontunai:not(.hidden)');
        assert(nonTunaiPanel !== null, 'Panel Midtrans Snap Payment Gateway tampil aktif');

        // Check if Snap pop-up iframe is rendered
        const snapIframe = await page.waitForSelector('#snap-midtrans', { timeout: 10000 });
        assert(snapIframe !== null, 'Pop-up Resmi Midtrans Snap berhasil terbuka otomatis');

        // Selesaikan pembayaran sukses Midtrans
        console.log('  ℹ️ Melakukan konfirmasi pembayaran sukses pada Midtrans Snap...');
        await page.evaluate(() => finalisasiPembayaranMidtrans('MDT-E2E-SUCCESS'));
        await page.waitForTimeout(1500);

        const bodyPaidMidtrans = await page.textContent('body');
        assert(bodyPaidMidtrans.includes('Non-Tunai'), 'Struk mencantumkan metode pembayaran: Non-Tunai');
        assert(bodyPaidMidtrans.includes('LUNAS') || bodyPaidMidtrans.includes('PAID'), 'Status pesanan Non-Tunai berhasil LUNAS (PAID)');

        // ==========================================
        // TEST 3: KASIR ROLE - TAMBAH MENU & AKSES STOK
        // ==========================================
        console.log('\n--- [TEST 3] Pengujian Kasir: Akses Kelola Menu & Tambah Menu Baru ---');
        await page.goto('http://127.0.0.1:8000/admin/stok', { waitUntil: 'domcontentloaded' });
        assert(!page.url().includes('/login') && page.url().includes('/admin/stok'), 'Kasir diizinkan mengakses halaman Kelola Menu & Stok');

        const testItemName = 'Es Kopi Susu Aren Gula Aren E2E';
        console.log(`  ℹ️ Kasir menambahkan menu baru: "${testItemName}"`);
        
        // Open modal
        await page.click('button:has-text("Tambah Menu Baru")');
        await page.waitForTimeout(400);

        // Fill form
        await page.fill('#modal-tambah-menu input[name="nama_produk"]', testItemName);
        await page.selectOption('#modal-tambah-menu select[name="kategori_id"]', '3'); // Minuman
        await page.fill('#modal-tambah-menu input[name="harga"]', '16000');
        await page.fill('#modal-tambah-menu input[name="stok"]', '35');
        await page.selectOption('#modal-tambah-menu select[name="status"]', 'Tersedia');
        await page.fill('#modal-tambah-menu input[name="deskripsi"]', 'Espresso dengan susu segar dan gula aren asli');

        await page.click('#modal-tambah-menu button[type="submit"]');
        await page.waitForURL('**/admin/stok**', { timeout: 10000 });

        const stokPageContent = await page.textContent('body');
        assert(stokPageContent.includes(testItemName), `Menu baru "${testItemName}" berhasil tersimpan dan tampil di daftar menu`);

        // Check if item is also visible in POS
        await page.goto('http://127.0.0.1:8000/kasir', { waitUntil: 'domcontentloaded' });
        const posPageContent = await page.textContent('body');
        assert(posPageContent.includes(testItemName), `Menu baru "${testItemName}" langsung muncul secara real-time di POS kasir`);

        // ==========================================
        // TEST 4: ADMIN ROLE - CRUD LENGKAP & REKAP LAPORAN PENJUALAN
        // ==========================================
        console.log('\n--- [TEST 4] Pengujian Administrator: Edit Menu, Hapus Menu, & Rekap Tunai vs Non-Tunai ---');
        // Switch to Admin
        await page.goto('http://127.0.0.1:8000/logout', { waitUntil: 'domcontentloaded' });
        await page.fill('input[name="username"]', 'admin');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/admin/stok**', { timeout: 10000 });

        assert(page.url().includes('/admin/stok'), 'Admin berhasil login ke Kelola Stok');

        // Test Update Menu (Edit modal)
        console.log(`  ℹ️ Admin melakukan edit data menu "${testItemName}"...`);
        const row = await page.$(`tr:has-text("${testItemName}")`);
        assert(row !== null, `Baris tabel untuk "${testItemName}" ditemukan`);

        if (row) {
            const editBtn = await row.$('button[title="Edit Detail Menu Lengkap"]');
            await editBtn.click();
            await page.waitForTimeout(400);

            // Edit price to 18000 and stock to 50
            await page.fill('#edit_harga', '18000');
            await page.fill('#edit_stok', '50');
            await page.click('#form-edit-menu button[type="submit"]');
            await page.waitForURL('**/admin/stok**', { timeout: 10000 });

            const updatedRow = await page.$(`tr:has-text("${testItemName}")`);
            const updatedPrice = await updatedRow.textContent();
            const updatedStok = await updatedRow.$eval('input[name="stok"]', el => el.value);
            assert(updatedPrice.includes('18.000'), `Harga menu berhasil di-update menjadi Rp 18.000`);
            assert(updatedStok === '50', `Stok menu berhasil di-update menjadi 50`);
        }

        // Test Delete Menu
        console.log(`  ℹ️ Admin melakukan penghapusan menu pengujian "${testItemName}"...`);
        const deleteRow = await page.$(`tr:has-text("${testItemName}")`);
        if (deleteRow) {
            page.once('dialog', async dialog => {
                await dialog.accept();
            });
            const deleteBtn = await deleteRow.$('button[title="Hapus Menu"]');
            await Promise.all([
                page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
                deleteBtn.click()
            ]);

            const postDeleteContent = await page.textContent('tbody');
            assert(!postDeleteContent.includes(testItemName), `Menu "${testItemName}" berhasil dihapus dari sistem (tidak ada di tabel)`);
        }

        // Test Laporan Penjualan (Khusus Admin)
        console.log('  ℹ️ Admin mengakses Laporan Penjualan...');
        await page.goto('http://127.0.0.1:8000/admin/laporan', { waitUntil: 'domcontentloaded' });
        const laporanHeading = await page.textContent('h2');
        assert(laporanHeading.includes('Rekapitulasi Laporan Penjualan'), 'Laporan Penjualan terbuka dengan rekap omzet');

        const laporanContent = await page.textContent('body');
        assert(laporanContent.includes('Total Omzet'), 'Statistik Total Omzet tertera');
        assert(laporanContent.includes('Tunai'), 'Rekapitulasi metode Tunai tertera');
        assert(laporanContent.includes('Non-Tunai') || laporanContent.includes('Debit / Kredit'), 'Rekapitulasi metode Non-Tunai tertera');

    } catch (err) {
        console.error('💥 TERJADI KESALAHAN PADA SUITE PENGUJIAN:', err);
        failedTests++;
    } finally {
        await browser.close();
    }

    console.log('\n=============================================');
    console.log(`📊 RINGKASAN PENGUJIAN: ${passedTests} BERHASIL, ${failedTests} GAGAL`);
    console.log('=============================================');
    process.exit(failedTests > 0 ? 1 : 0);
})();
