<div class="container">
    <h2 class="mb-4">🎯 Metode AHP (Analytical Hierarchy Process)</h2>
    
    <div class="alert alert-info">
        <strong>Cara Kerja:</strong> Masukkan nama alternatif, kriteria dengan bobotnya, kemudian nilai setiap alternatif untuk masing-masing kriteria.
    </div>

    <form method="post" action="<?= base_url('ahp/calculate') ?>" id="ahpForm">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
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

        <button type="submit" class="btn btn-primary btn-lg w-100" id="calculateBtn" style="display:none;">
            Hitung Hasil AHP
        </button>
    </form>

    <?php if (isset($results)): ?>
    <div class="result-card mt-5">
        <h3 class="mb-4">📊 Proses & Hasil Perhitungan AHP</h3>
        
        <!-- STEP 1: Data Input -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📝 STEP 1: Data Input</h5>
            </div>
            <div class="card-body">
                <h6 class="text-info">Alternatif:</h6>
                <div class="mb-3">
                    <?php foreach ($alternatives as $index => $alt): ?>
                        <span class="badge bg-primary me-2">A<?= $index + 1 ?>: <?= esc($alt) ?></span>
                    <?php endforeach; ?>
                </div>

                <h6 class="text-info mt-3">Kriteria & Bobot:</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <?php foreach ($criteria as $crit): ?>
                                <th><?= esc($crit) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php foreach ($weights as $w): ?>
                                <td><strong><?= $w ?></strong></td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="text-info mt-3">Matriks Nilai Alternatif:</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
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

        <!-- STEP 2: Normalisasi Bobot -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">🔢 STEP 2: Normalisasi Bobot Kriteria</h5>
            </div>
            <div class="card-body">
                <p><strong>Formula:</strong> Bobot Ternormalisasi = Bobot / Total Bobot</p>
                <p><strong>Total Bobot:</strong> <span class="badge bg-warning text-dark"><?= number_format($step2['totalWeight'], 4) ?></span></p>
                
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Kriteria</th>
                                <th>Bobot Awal</th>
                                <th>Perhitungan</th>
                                <th>Bobot Ternormalisasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($criteria as $i => $crit): ?>
                            <tr>
                                <td><strong><?= esc($crit) ?></strong></td>
                                <td><?= $weights[$i] ?></td>
                                <td><code><?= $weights[$i] ?> / <?= number_format($step2['totalWeight'], 4) ?></code></td>
                                <td><span class="badge bg-success"><?= number_format($normalizedWeights[$i], 4) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3">Total Bobot Ternormalisasi:</th>
                                <th><span class="badge bg-primary"><?= number_format(array_sum($normalizedWeights), 4) ?></span></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- STEP 3: Perhitungan Skor -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">📐 STEP 3: Perhitungan Skor Alternatif</h5>
            </div>
            <div class="card-body">
                <p><strong>Formula:</strong> Skor = Σ (Nilai × Bobot Ternormalisasi)</p>
                
                <?php foreach ($calculations as $altName => $calc): ?>
                <div class="card mb-3 bg-dark">
                    <div class="card-header bg-secondary">
                        <h6 class="mb-0 text-white">Perhitungan untuk: <strong><?= esc($altName) ?></strong></h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kriteria</th>
                                        <th>Nilai</th>
                                        <th>Bobot</th>
                                        <th>Perhitungan</th>
                                        <th>Kontribusi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($calc['details'] as $detail): ?>
                                    <tr>
                                        <td><strong><?= esc($detail['criteria']) ?></strong></td>
                                        <td><?= number_format($detail['value'], 2) ?></td>
                                        <td><?= number_format($detail['weight'], 4) ?></td>
                                        <td><code><?= number_format($detail['value'], 2) ?> × <?= number_format($detail['weight'], 4) ?></code></td>
                                        <td><span class="badge bg-info"><?= number_format($detail['contribution'], 4) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-success">
                                    <tr>
                                        <th colspan="4">Total Skor:</th>
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

        <!-- STEP 4: Hasil Akhir -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">🏆 STEP 4: Peringkat Final</h5>
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
            <input type="text" name="alternatives[]" class="form-control" placeholder="Contoh: Laptop A" required>
        </div>`;
    }
    html += '</div></div></div>';
    
    html += '<div class="card mb-4"><div class="card-header bg-secondary text-white"><h5 class="mb-0">3. Kriteria dan Bobot</h5></div><div class="card-body">';
    html += '<div class="table-responsive"><table class="table table-bordered"><thead class="table-light"><tr><th>Kriteria</th><th>Bobot</th></tr></thead><tbody>';
    
    for (let i = 0; i < numCrits; i++) {
        html += `<tr>
            <td><input type="text" name="criteria[]" class="form-control" placeholder="Contoh: Harga" required></td>
            <td><input type="number" name="weights[]" class="form-control" step="0.01" min="0" value="1" required></td>
        </tr>`;
    }
    html += '</tbody></table></div><small class="text-muted">*Bobot akan dinormalisasi otomatis (total = 1)</small></div></div>';
    
    html += '<div class="card mb-4"><div class="card-header bg-info text-white"><h5 class="mb-0">4. Nilai Alternatif untuk Setiap Kriteria</h5></div><div class="card-body">';
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