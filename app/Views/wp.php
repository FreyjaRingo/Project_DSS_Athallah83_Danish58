<div class="container">
    <h2 class="mb-4">📊 Metode WP (Weighted Product)</h2>
    
    <div class="alert alert-warning">
        <strong>Cara Kerja:</strong> WP menggunakan perkalian untuk menghubungkan rating atribut dengan pangkat bobot yang telah dinormalisasi.
    </div>

    <form method="post" action="<?= base_url('wp/calculate/') ?>" id="wpForm">
        <?= csrf_field() ?>
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">1. Input Jumlah Data</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jumlah Alternatif:</label>
                        <input type="number" class="form-control" id="numAlternatives" min="2" max="10" value="3">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jumlah Kriteria:</label>
                        <input type="number" class="form-control" id="numCriteria" min="2" max="10" value="3">
                    </div>
                </div>
                <button type="button" class="btn btn-secondary" onclick="generateInputs()">Generate Form</button>
            </div>
        </div>

        <div id="dynamicInputs"></div>

        <button type="submit" class="btn btn-warning text-dark btn-lg w-100" id="calculateBtn" style="display:none;">
            Hitung Hasil WP
        </button>
    </form>

    <?php if (isset($results)): ?>
    <div class="result-card mt-5">
        <h3 class="mb-4">📊 Proses & Hasil Perhitungan WP</h3>
        
        <!-- STEP 1 -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📝 STEP 1: Data Input</h5>
            </div>
            <div class="card-body">
                <h6 class="text-info">Bobot & Tipe Kriteria:</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <?php foreach ($criteria as $j => $crit): ?>
                                <th><?= esc($crit) ?><br><small>(<?= $step1['criteriaTypes'][$j] ?>)</small></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php foreach ($step1['weights'] as $w): ?>
                                <td><strong><?= $w ?></strong></td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="text-info mt-3">Matriks Keputusan:</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Alternatif</th>
                                <?php foreach ($criteria as $crit): ?>
                                <th><?= esc($crit) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alternatives as $i => $alt): ?>
                            <tr>
                                <th class="table-light"><?= esc($alt) ?></th>
                                <?php foreach ($criteria as $j => $crit): ?>
                                <td><?= $step1['matrix'][$i][$j] ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- STEP 2 -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">⚖️ STEP 2: Perbaikan Bobot (Normalisasi)</h5>
            </div>
            <div class="card-body">
                <p><strong>Formula:</strong> w<sub>j</sub> = W<sub>j</sub> / ΣW<sub>j</sub></p>
                <p><strong>Catatan:</strong> Jika kriteria = <span class="badge bg-danger">Cost</span>, maka bobot menjadi negatif</p>
                <p><strong>Total Bobot Awal:</strong> <span class="badge bg-warning text-dark"><?= number_format($step2['totalWeight'], 4) ?></span></p>
                
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Kriteria</th>
                                <th>Bobot Awal (W)</th>
                                <th>Tipe</th>
                                <th>Bobot Ternormalisasi (w)</th>
                                <th>Bobot Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($step2['weightDetails'] as $critName => $detail): ?>
                            <tr>
                                <td><strong><?= esc($critName) ?></strong></td>
                                <td><?= number_format($detail['original'], 2) ?></td>
                                <td>
                                    <span class="badge <?= $detail['type'] === 'benefit' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ucfirst($detail['type']) ?>
                                    </span>
                                </td>
                                <td><code><?= number_format($detail['original'], 2) ?> / <?= number_format($step2['totalWeight'], 4) ?> = <?= number_format($detail['normalized'], 4) ?></code></td>
                                <td>
                                    <span class="badge <?= $detail['finalWeight'] >= 0 ? 'bg-success' : 'bg-danger' ?>">
                                        <?= number_format($detail['finalWeight'], 4) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4">Total Bobot Ternormalisasi (tanpa tanda):</th>
                                <th><span class="badge bg-primary">1.0000</span></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- STEP 3 -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">📐 STEP 3: Perhitungan Vektor S</h5>
            </div>
            <div class="card-body">
                <p><strong>Formula:</strong> S<sub>i</sub> = Π(x<sub>ij</sub><sup>w<sub>j</sub></sup>) = x<sub>i1</sub><sup>w<sub>1</sub></sup> × x<sub>i2</sub><sup>w<sub>2</sub></sup> × ... × x<sub>in</sub><sup>w<sub>n</sub></sup></p>
                
                <?php foreach ($step3['vectorSDetails'] as $altName => $calc): ?>
                <div class="card mb-3 bg-dark">
                    <div class="card-header bg-secondary">
                        <h6 class="mb-0 text-white">Perhitungan Vektor S: <strong><?= esc($altName) ?></strong></h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kriteria</th>
                                        <th>Nilai (x)</th>
                                        <th>Bobot (w)</th>
                                        <th>Perhitungan</th>
                                        <th>Hasil (x<sup>w</sup>)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($calc['details'] as $detail): ?>
                                    <tr>
                                        <td><strong><?= esc($detail['criteria']) ?></strong></td>
                                        <td><?= number_format($detail['value'], 2) ?></td>
                                        <td><?= number_format($detail['weight'], 4) ?></td>
                                        <td><code><?= number_format($detail['value'], 2) ?><sup><?= number_format($detail['weight'], 4) ?></sup></code></td>
                                        <td><span class="badge bg-info"><?= number_format($detail['result'], 4) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-success">
                                    <tr>
                                        <th colspan="4">S (Hasil Perkalian):</th>
                                        <th><span class="badge bg-success"><?= number_format($calc['vectorS'], 4) ?></span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="alert alert-info mt-3">
                    <strong>Ringkasan Vektor S:</strong><br>
                    <?php foreach ($step3['vectorSDetails'] as $altName => $calc): ?>
                        <span class="badge bg-primary me-2"><?= esc($altName) ?>: <?= number_format($calc['vectorS'], 4) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- STEP 4 -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">🎯 STEP 4: Perhitungan Vektor V (Preferensi)</h5>
            </div>
            <div class="card-body">
                <p><strong>Formula:</strong> V<sub>i</sub> = S<sub>i</sub> / ΣS<sub>i</sub></p>
                <p><strong>Total ΣS:</strong> <span class="badge bg-warning text-dark"><?= number_format($step4['totalS'], 4) ?></span></p>
                
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Alternatif</th>
                                <th>Vektor S</th>
                                <th>Total ΣS</th>
                                <th>Perhitungan</th>
                                <th>Vektor V (Preferensi)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($step4['vectorVDetails'] as $altName => $detail): ?>
                            <tr>
                                <td><strong><?= esc($altName) ?></strong></td>
                                <td><?= number_format($detail['vectorS'], 4) ?></td>
                                <td><?= number_format($detail['totalS'], 4) ?></td>
                                <td><code><?= number_format($detail['vectorS'], 4) ?> / <?= number_format($detail['totalS'], 4) ?></code></td>
                                <td><span class="badge bg-success"><?= number_format($detail['vectorV'], 4) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4">Total Vektor V:</th>
                                <th><span class="badge bg-primary">1.0000</span></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Hasil Final -->
        <div class="card">
            <div class="card-header text-white" style="background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%) !important;">
                <h5 class="mb-0">🏆 HASIL AKHIR: Peringkat Alternatif</h5>
            </div>
            <div class="card-body">
                <?php foreach ($results as $index => $result): ?>
                <div class="p-3 mb-2 rounded <?= $index < 3 ? 'rank-'.($index+1) : 'bg-light' ?>">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <span class="badge bg-dark me-2">#<?= $index + 1 ?></span>
                                <?= esc($result['name']) ?>
                            </h5>
                        </div>
                        <div>
                            <h4 class="mb-0"><?= number_format($result['score'], 4) ?></h4>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function generateInputs() {
    const numAlts = parseInt(document.getElementById('numAlternatives').value);
    const numCrits = parseInt(document.getElementById('numCriteria').value);
    
    let html = '<div class="card mb-4"><div class="card-header bg-secondary text-white"><h5 class="mb-0">2. Nama Alternatif</h5></div><div class="card-body"><div class="row">';
    
    for (let i = 0; i < numAlts; i++) {
        html += `<div class="col-md-4 mb-3">
            <label class="form-label">Alternatif ${i+1}:</label>
            <input type="text" name="alternatives[]" class="form-control" placeholder="Contoh: Supplier A" required>
        </div>`;
    }
    html += '</div></div></div>';
    
    html += '<div class="card mb-4"><div class="card-header bg-secondary text-white"><h5 class="mb-0">3. Kriteria, Bobot, dan Tipe</h5></div><div class="card-body">';
    html += '<div class="table-responsive"><table class="table table-bordered"><thead class="table-light"><tr><th>Kriteria</th><th>Bobot</th><th>Tipe</th></tr></thead><tbody>';
    
    for (let i = 0; i < numCrits; i++) {
        html += `<tr>
            <td><input type="text" name="criteria[]" class="form-control" placeholder="Contoh: Kualitas" required></td>
            <td><input type="number" name="weights[]" class="form-control" step="0.01" min="0" value="1" required></td>
            <td>
                <select name="criteria_types[]" class="form-select" required>
                    <option value="benefit">Benefit (Semakin Tinggi Semakin Baik)</option>
                    <option value="cost">Cost (Semakin Rendah Semakin Baik)</option>
                </select>
            </td>
        </tr>`;
    }
    html += '</tbody></table></div><small class="text-muted">*Bobot akan dinormalisasi otomatis</small></div></div>';
    
    html += '<div class="card mb-4"><div class="card-header bg-info text-white"><h5 class="mb-0">4. Nilai Alternatif untuk Setiap Kriteria</h5></div><div class="card-body">';
    html += '<div class="table-responsive"><table class="table table-bordered"><thead class="table-light"><tr><th>Alternatif</th>';
    
    for (let i = 0; i < numCrits; i++) {
        html += `<th>Kriteria ${i+1}</th>`;
    }
    html += '</tr></thead><tbody>';
    
    for (let i = 0; i < numAlts; i++) {
        html += `<tr><th class="table-light">Alt. ${i+1}</th>`;
        for (let j = 0; j < numCrits; j++) {
            html += `<td><input type="number" name="matrix[${i}][${j}]" class="form-control" step="any" min="0.0001" value="1" required></td>`;
        }
        html += '</tr>';
    }
    html += '</tbody></table></div></div></div>';
    
    document.getElementById('dynamicInputs').innerHTML = html;
    document.getElementById('calculateBtn').style.display = 'block';
}
</script>