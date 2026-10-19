{{-- Privacy policy. The contact block reads the same settings the console
     edits, so changing the WhatsApp number in Setting updates this page too. --}}
@extends('layouts.app')

@section('title', 'Kebijakan Privasi')

@section('meta-description', 'Bagaimana Penida Gili mengumpulkan, menggunakan, dan melindungi data pribadi pengunjung dan tamu.')

@php
    $h2 = 'mt-[48px] text-[24px] font-bold leading-[34px] text-ink lg:text-[30px] lg:leading-[42px]';
    $h3 = 'mt-[28px] text-[18px] font-semibold leading-[28px] text-ink lg:text-[20px]';
    $p = 'mt-[16px] text-[16px] leading-[30px] text-ink-muted lg:text-[18px] lg:leading-[34px]';
    $ul = 'mt-[16px] flex list-disc flex-col gap-[8px] ps-[24px] text-[16px] leading-[30px] text-ink-muted lg:text-[18px] lg:leading-[34px]';
@endphp

@section('hero')
    @include('partials.page-hero', [
        'image' => asset('images/articles/hero-article.png'),
        'title' => 'Kebijakan Privasi',
        'subtitle' => 'Bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda.',
        'active' => null,
    ])
@endsection

@section('content')
    <section class="container-page pb-[90px] pt-[48px] lg:pb-[140px] lg:pt-[72px]">
        <article class="mx-auto max-w-[860px]">
            <p class="text-[14px] leading-[22px] text-ink-muted">Terakhir diperbarui: {{ $updatedAt }}</p>

            <h2 class="{{ $h2 }}">1. Pendahuluan</h2>
            <p class="{{ $p }}">
                Kebijakan Privasi ini menjelaskan bagaimana Penida Gili mengumpulkan, menggunakan, menyimpan, dan
                melindungi data pribadi Anda ketika mengakses situs ini, mengajukan pemesanan, atau menghubungi kami.
            </p>
            <p class="{{ $p }}">
                Kami mengelola data pribadi sesuai Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi.
            </p>

            <h2 class="{{ $h2 }}">2. Data yang Kami Kumpulkan</h2>

            <h3 class="{{ $h3 }}">a. Data pemesanan</h3>
            <p class="{{ $p }}">Saat Anda mengisi formulir pemesanan tiket kapal, aktivitas, atau hotel, kami menyimpan:</p>
            <ul class="{{ $ul }}">
                <li>Nama lengkap</li>
                <li>Nomor telepon beserta kode negaranya</li>
                <li>Alamat email, jika Anda mengisinya</li>
                <li>Kewarganegaraan</li>
                <li>Tanggal perjalanan, jumlah dewasa dan anak, serta rute, kamar, atau aktivitas yang dipilih</li>
                <li>Catatan tambahan yang Anda tulis sendiri pada formulir</li>
            </ul>

            <h3 class="{{ $h3 }}">b. Alamat email buletin</h3>
            <p class="{{ $p }}">
                Jika Anda mendaftar buletin di bagian bawah situs, kami menyimpan alamat email Anda beserta
                keterangan dari halaman mana pendaftaran itu berasal.
            </p>

            <h3 class="{{ $h3 }}">c. Percakapan WhatsApp</h3>
            <p class="{{ $p }}">
                Tombol WhatsApp di situs ini membuka aplikasi WhatsApp dengan pesan pembuka yang sudah terisi. Isi
                percakapan selanjutnya tersimpan di WhatsApp dan di perangkat tim kami, dan tunduk pada kebijakan
                privasi WhatsApp.
            </p>

            <h3 class="{{ $h3 }}">d. Data teknis</h3>
            <p class="{{ $p }}">
                Server kami mencatat data kunjungan seperti alamat IP, jenis peramban, dan halaman yang diakses.
                Catatan ini dipakai untuk menjaga keamanan dan menelusuri gangguan teknis.
            </p>

            <h2 class="{{ $h2 }}">3. Cookie</h2>
            <p class="{{ $p }}">
                Situs ini hanya menggunakan cookie yang diperlukan agar situs berfungsi: cookie sesi dan cookie
                keamanan formulir. Keduanya tidak digunakan untuk iklan maupun pelacakan lintas situs.
            </p>
            <p class="{{ $p }}">
                Saat ini kami tidak memasang layanan analitik pihak ketiga seperti Google Analytics. Apabila nanti
                kami memasangnya, halaman ini akan diperbarui lebih dahulu.
            </p>

            <h2 class="{{ $h2 }}">4. Untuk Apa Data Digunakan</h2>
            <ul class="{{ $ul }}">
                <li>Memproses dan mengonfirmasi pemesanan Anda</li>
                <li>Menghubungi Anda mengenai perjalanan tersebut, termasuk perubahan jadwal</li>
                <li>Meneruskan data yang diperlukan kepada operator kapal, hotel, atau penyedia aktivitas yang Anda pesan</li>
                <li>Mengirimkan buletin, jika Anda mendaftar</li>
                <li>Menjaga keamanan situs dan memenuhi kewajiban hukum</li>
            </ul>
            <p class="{{ $p }}">
                Kami tidak menggunakan data Anda untuk tujuan di luar daftar ini tanpa memberi tahu Anda lebih dahulu.
            </p>

            <h2 class="{{ $h2 }}">5. Dengan Siapa Data Dibagikan</h2>
            <p class="{{ $p }}">Kami tidak menjual data pribadi Anda. Data dibagikan terbatas kepada:</p>
            <ul class="{{ $ul }}">
                <li><strong class="text-ink">Mitra layanan</strong> — operator kapal, hotel, atau penyedia aktivitas, sebatas data yang mereka perlukan untuk melayani pemesanan Anda</li>
                <li><strong class="text-ink">Penyedia teknologi</strong> — penyedia server tempat situs ini berjalan</li>
                <li><strong class="text-ink">WhatsApp</strong> — jika Anda memilih menghubungi kami lewat saluran itu</li>
                <li><strong class="text-ink">Pihak berwenang</strong> — apabila diwajibkan oleh hukum</li>
            </ul>

            <h2 class="{{ $h2 }}">6. Berapa Lama Data Disimpan</h2>
            <p class="{{ $p }}">
                Data pemesanan disimpan selama diperlukan untuk melayani perjalanan Anda, menyelesaikan keluhan
                apabila ada, dan memenuhi kewajiban pembukuan. Alamat email buletin disimpan sampai Anda meminta
                berhenti berlangganan. Setelah tidak diperlukan, data dihapus atau dianonimkan.
            </p>

            <h2 class="{{ $h2 }}">7. Keamanan</h2>
            <p class="{{ $p }}">
                Akses ke data pemesanan dibatasi pada akun pengelola yang berwenang dan dilindungi kata sandi. Situs
                ini diakses melalui koneksi terenkripsi. Meski begitu, tidak ada pengiriman data lewat internet yang
                sepenuhnya bebas risiko.
            </p>
            <p class="{{ $p }}">
                Jika terjadi kebocoran data pribadi, kami akan memberitahukannya sesuai kewajiban dalam peraturan
                yang berlaku.
            </p>

            <h2 class="{{ $h2 }}">8. Hak Anda</h2>
            <p class="{{ $p }}">Anda berhak untuk:</p>
            <ul class="{{ $ul }}">
                <li>Mengetahui data apa saja tentang Anda yang kami simpan dan meminta salinannya</li>
                <li>Memperbaiki data yang keliru</li>
                <li>Meminta data Anda dihapus, sepanjang tidak bertentangan dengan kewajiban hukum kami</li>
                <li>Menarik persetujuan, misalnya berhenti berlangganan buletin</li>
                <li>Mengajukan keluhan kepada kami atau kepada otoritas yang berwenang</li>
            </ul>
            <p class="{{ $p }}">
                Hubungi kami lewat kontak di bawah untuk menggunakan hak ini. Kami dapat meminta keterangan tambahan
                untuk memastikan permintaan benar berasal dari Anda.
            </p>

            <h2 class="{{ $h2 }}">9. Tautan ke Situs Lain</h2>
            <p class="{{ $p }}">
                Situs ini memuat tautan ke layanan pihak lain seperti WhatsApp, Instagram, dan YouTube. Kebijakan
                privasi mereka berlaku saat Anda berada di sana, dan kami tidak mengendalikannya.
            </p>

            <h2 class="{{ $h2 }}">10. Data Anak</h2>
            <p class="{{ $p }}">
                Pemesanan di situs ini ditujukan untuk orang dewasa. Data anak yang ikut bepergian hanya kami terima
                sebatas jumlah peserta anak pada pemesanan, yang diisi oleh orang tua atau walinya.
            </p>

            <h2 class="{{ $h2 }}">11. Perubahan Kebijakan</h2>
            <p class="{{ $p }}">
                Kebijakan ini dapat diperbarui bila layanan atau ketentuan hukum berubah. Tanggal pembaruan
                terakhir selalu tercantum di bagian atas halaman ini.
            </p>

            <h2 class="{{ $h2 }}">12. Hubungi Kami</h2>
            <p class="{{ $p }}">Untuk pertanyaan atau permintaan terkait data pribadi Anda:</p>
            <ul class="{{ $ul }}">
                <li>Email: <a href="mailto:{{ config('penida.booking.email') }}" class="text-brand hover:underline">{{ config('penida.booking.email') }}</a></li>
                <li>WhatsApp: <a href="https://wa.me/{{ config('penida.booking.whatsapp') }}" target="_blank" rel="noopener" class="text-brand hover:underline">+{{ config('penida.booking.whatsapp') }}</a></li>
            </ul>
        </article>
    </section>
@endsection
