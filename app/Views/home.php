<div class="container">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-primary">Decision Support System</h1>
        <p class="lead text-muted">Simulasi 4 Metode Pengambilan Keputusan</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-primary">
                        <i class="bi bi-hierarchy"></i> AHP
                    </h3>
                    <h6 class="text-muted mb-3">Analytical Hierarchy Process</h6>
                    <p class="card-text">
                        Metode pengambilan keputusan dengan perbandingan berpasangan untuk menentukan bobot kriteria dan alternatif secara hierarkis.
                    </p>
                    <ul class="list-unstyled">
                        <li>✓ Perbandingan berpasangan</li>
                        <li>✓ Konsistensi rasio</li>
                        <li>✓ Bobot prioritas</li>
                    </ul>
                    <a href="<?= base_url('ahp') ?>" class="btn btn-primary mt-3">Mulai Simulasi →</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-success">
                        <i class="bi bi-arrow-up-right-circle"></i> TOPSIS
                    </h3>
                    <h6 class="text-muted mb-3">Technique for Order Preference by Similarity to Ideal Solution</h6>
                    <p class="card-text">
                        Metode yang menggunakan jarak geometris terhadap solusi ideal positif dan negatif untuk menentukan alternatif terbaik.
                    </p>
                    <ul class="list-unstyled">
                        <li>✓ Normalisasi vektor</li>
                        <li>✓ Solusi ideal</li>
                        <li>✓ Jarak Euclidean</li>
                    </ul>
                    <a href="<?= base_url('topsis') ?>" class="btn btn-success mt-3">Mulai Simulasi →</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-info">
                        <i class="bi bi-calculator"></i> SAW
                    </h3>
                    <h6 class="text-muted mb-3">Simple Additive Weighting</h6>
                    <p class="card-text">
                        Metode penjumlahan terbobot yang sederhana dengan normalisasi nilai untuk setiap alternatif pada semua kriteria.
                    </p>
                    <ul class="list-unstyled">
                        <li>✓ Normalisasi linear</li>
                        <li>✓ Penjumlahan terbobot</li>
                        <li>✓ Mudah dipahami</li>
                    </ul>
                    <a href="<?= base_url('saw') ?>" class="btn btn-info text-white mt-3">Mulai Simulasi →</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-warning">
                        <i class="bi bi-bar-chart"></i> WP
                    </h3>
                    <h6 class="text-muted mb-3">Weighted Product</h6>
                    <p class="card-text">
                        Metode perkalian untuk menghubungkan rating atribut dengan menggunakan pangkat bobot yang sudah dinormalisasi.
                    </p>
                    <ul class="list-unstyled">
                        <li>✓ Perkalian terbobot</li>
                        <li>✓ Pangkat normalisasi</li>
                        <li>✓ Vektor preferensi</li>
                    </ul>
                    <a href="<?= base_url('wp') ?>" class="btn btn-warning text-dark mt-3">Mulai Simulasi →</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card border-primary border-3 shadow-lg">
                <div class="card-body text-center p-4">
                    <h3 class="card-title text-primary mb-3">
                        <i class="bi bi-arrow-left-right"></i> 🔄 Fitur Perbandingan Metode
                    </h3>
                    <p class="lead mb-3">
                        <strong>Input sekali, bandingkan dua metode sekaligus!</strong>
                    </p>
                    <p class="text-muted mb-4">
                        Bandingkan hasil dari 2 metode DSS yang berbeda untuk melihat konsistensi peringkat alternatif. 
                        Cocok untuk validasi hasil dan analisis mendalam.
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded">
                                <h5 class="text-primary mb-2">📝 Input Sekali</h5>
                                <p class="small mb-0">Cukup input data satu kali untuk kedua metode</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded">
                                <h5 class="text-success mb-2">📊 Hasil Side-by-Side</h5>
                                <p class="small mb-0">Lihat perbandingan hasil secara berdampingan</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded">
                                <h5 class="text-info mb-2">📈 Analisis Otomatis</h5>
                                <p class="small mb-0">Dapatkan analisis konsistensi secara otomatis</p>
                            </div>
                        </div>
                    </div>
                    <a href="<?= base_url('compare') ?>" class="btn btn-primary btn-lg px-5">
                        🔍 Mulai Perbandingan →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 p-4 bg-light rounded">
        <h4 class="text-center mb-3">TUGAS DECISION SUPPORT SYSTEM</h4>
        <p class="text-center text-muted">
            Dibuat oleh:
            Danish Rahadian Mirza Effendi - 140810230058
            Athallah Azhar Aulia Hadi - 140810230083
        </p>
    </div>
</div>