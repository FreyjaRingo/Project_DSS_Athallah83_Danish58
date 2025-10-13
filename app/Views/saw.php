<div class="container">
    <h2 class="mb-4">🧮 Metode SAW (Simple Additive Weighting)</h2>
    
    <div class="alert alert-info">
        <strong>Cara Kerja:</strong> SAW melakukan normalisasi matriks keputusan kemudian melakukan penjumlahan terbobot untuk mendapatkan nilai preferensi.
    </div>

    <form method="post" action="<?= base_url('saw/calculate') ?>" id="sawForm">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
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

        <button type="submit" class="btn btn-info text-white btn-lg w-100" id="calculateBtn" style="display:none;">
            Hitung Hasil SAW
        </button>
    </form>

    <?php if (isset($results)): ?>
    <div class="result-card mt-5">
        <h3 class="mb-4">📊 Proses & Hasil Perhitungan SAW</h3>
        
        <!-- STEP 1 -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📝 STEP 1: Matriks Keputusan & Bobot</h5>
            </div>
            <div class="card-body">
                <h6 class="text-info">Bobot Kriteria:</h6>
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
                <h5 class="mb-0">🔍 STEP 2: Nilai Max/Min per Kriteria</h5>
            </div>
            <div class="card-body">
                <p><strong>Benefit:</strong> Cari nilai maksimum | <strong>Cost:</strong> Cari nilai minimum</p>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Kriteria</th>
                                <th>Tipe</th>
                                <th>Nilai Max</th>
                                <th>Nilai Min</th>
                                <th>Digunakan untuk Normalisasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($criteria as $j => $crit): ?>
                            <tr>
                                <td><strong><?= esc($crit) ?></strong></td>
                                <td>
                                    <span class="badge <?= $step2['maxMinValues'][$j]['type'] === 'benefit' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ucfirst($step2['maxMinValues'][$j]['type']) ?>
                                    </span>
                                </td>
                                <td><?= number_format($step2['maxMinValues'][$j]['max'], 2) ?></td>
                                <td><?= number_format($step2['maxMinValues'][$j]['min'], 2) ?></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?= $step2['maxMinValues'][$j]['type'] === 'benefit' ? 
                                            number_format($step2['maxMinValues'][$j]['max'], 2) : 
                                            number_format($step2['maxMinValues'][$j]['min'], 2) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- STEP 3 -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">📐 STEP 3: Normalisasi Matriks</h5>
            </div>
            <div class="card-body">
                <p><strong>Benefit:</strong> r<sub>ij</sub> = x<sub>ij</sub> / max(x<sub>ij</sub>)</p>
                <p><strong>Cost:</strong> r<sub>ij</sub> = min(x<sub>ij</sub>) / x<sub>ij</sub></p>
                
                <?php foreach ($step3['normalizationDetails'] as $altName => $details): ?>
                <div class="card mb-3 bg-dark">
                    <div class="card-header bg-secondary">
                        <h6 class="mb-0 text-white">Normalisasi: <strong><?= esc($altName) ?></strong></h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kriteria</th>
                                        <th>Nilai Awal</th>
                                        <th>Formula</th>
                                        <th>Nilai Ternormalisasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($details as $critName => $detail): ?>
                                    <tr>
                                        <td><strong><?= esc($critName) ?></strong></td>
                                        <td><?= number_format($detail['original'], 2) ?></td>
                                        <td><code><?= $detail['formula'] ?></code></td>
                                        <td><span class="badge bg-success"><?= number_format($detail['normalized'], 4) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <h6 class="text-info mt-4">Matriks Ternormalisasi (R):</h6>
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
                                <td><?= number_format($step3['normalizedMatrix'][$i][$j], 4) ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- STEP 4 -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">🎯 STEP 4: Perhitungan Skor Preferensi</h5>
            </div>
            <div class="card-body">
                <p><strong>Formula:</strong> V<sub>i</sub> = Σ(w<sub>j</sub> × r<sub>ij</sub>)</p>
                
                <?php foreach ($step4['scoreDetails'] as $altName => $calc): ?>
                <div class="card mb-3 bg-dark">
                    <div class="card-header bg-secondary">
                        <h6 class="mb-0 text-white">Perhitungan Skor: <strong><?= esc($altName) ?></strong></h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kriteria</th>
                                        <th>Ternormalisasi (r)</th>
                                        <th>Bobot (w)</th>
                                        <th>Perhitungan</th>
                                        <th>Kontribusi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($calc['details'] as $detail): ?>
                                    <tr>
                                        <td><strong><?= esc($detail['criteria']) ?></strong></td>
                                        <td><?= number_format($detail['normalized'], 4) ?></td>
                                        <td><?= number_format($detail['weight'], 2) ?></td>
                                        <td><code><?= number_format($detail['weight'], 2) ?> × <?= number_format($detail['normalized'], 4) ?></code></td>
                                        <td><span class="badge bg-info"><?= number_format($detail['contribution'], 4) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-success">
                                    <tr>
                                        <th colspan="4">Total Skor (V):</th>
                                        <th><span class="badge bg-success"><?= number_format($calc['totalScore'], 4) ?></span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Hasil Final -->
        <div class="card">
            <div class="card-header text-white" style="background: linear-gradient(135deg, #00ff88 0%, #00d9ff 100%) !important;">
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
            <input type="text" name="alternatives[]" class="form-control" placeholder="Contoh: Karyawan A" required>
        </div>`;
    }
    html += '</div></div></div>';
    
    html += '<div class="card mb-4"><div class="card-header bg-secondary text-white"><h5 class="mb-0">3. Kriteria, Bobot, dan Tipe</h5></div><div class="card-body">';
    html += '<div class="table-responsive"><table class="table table-bordered"><thead class="table-light"><tr><th>Kriteria</th><th>Bobot</th><th>Tipe</th></tr></thead><tbody>';
    
    for (let i = 0; i < numCrits; i++) {
        html += `<tr>
            <td><input type="text" name="criteria[]" class="form-control" placeholder="Contoh: Kedisiplinan" required></td>
            <td><input type="number" name="weights[]" class="form-control" step="0.01" min="0" value="0.2" required></td>
            <td>
                <select name="criteria_types[]" class="form-select" required>
                    <option value="benefit">Benefit (Semakin Tinggi Semakin Baik)</option>
                    <option value="cost">Cost (Semakin Rendah Semakin Baik)</option>
                </select>
            </td>
        </tr>`;
    }
    html += '</tbody></table></div><small class="text-muted">*Total bobot sebaiknya = 1</small></div></div>';
    
    html += '<div class="card mb-4"><div class="card-header bg-warning text-dark"><h5 class="mb-0">4. Nilai Alternatif untuk Setiap Kriteria</h5></div><div class="card-body">';
    html += '<div class="table-responsive"><table class="table table-bordered"><thead class="table-light"><tr><th>Alternatif</th>';
    
    for (let i = 0; i < numCrits; i++) {
        html += `<th>Kriteria ${i+1}</th>`;
    }
    html += '</tr></thead><tbody>';
    
    for (let i = 0; i < numAlts; i++) {
        html += `<tr><th class="table-light">Alt. ${i+1}</th>`;
        for (let j = 0; j < numCrits; j++) {
            html += `<td><input type="number" name="matrix[${i}][${j}]" class="form-control" step="0.01" value="0" required></td>`;
        }
        html += '</tr>';
    }
    html += '</tbody></table></div></div></div>';
    
    document.getElementById('dynamicInputs').innerHTML = html;
    document.getElementById('calculateBtn').style.display = 'block';
}
</script>