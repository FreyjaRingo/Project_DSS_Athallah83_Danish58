<?php if (isset($results)): ?>
    <div class="result-card mt-5">
        <h3 class="mb-4">📊 Proses & Hasil Perhitungan TOPSIS</h3>
        
        <!-- STEP 1 -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📝 STEP 1: Matriks Keputusan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Alternatif</th>
                                <?php foreach ($criteria as $j => $crit): ?>
                                <th><?= esc($crit) ?><br><small class="text-muted">(<?= $step4['criteriaTypes'][$j] ?>)</small></th>
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
                <h5 class="mb-0">🔢 STEP 2: Normalisasi Matriks</h5>
            </div>
            <div class="card-body">
                <p><strong>Formula:</strong> r<sub>ij</sub> = x<sub>ij</sub> / √(Σx<sub>ij</sub>²)</p>
                
                <h6 class="text-info mt-3">Akar Jumlah Kuadrat per Kolom:</h6>
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
                                <?php foreach ($step2['columnSums'] as $sum): ?>
                                <td><span class="badge bg-warning text-dark"><?= number_format($sum, 4) ?></span></td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="text-info mt-4">Matriks Ternormalisasi:</h6>
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
                                <td><?= number_format($step2['normalizedMatrix'][$i][$j], 4) ?></td>
                                <?php endforeach; ?>
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
                <h5 class="mb-0">⚖️ STEP 3: Matriks Ternormalisasi Terbobot</h5>
            </div>
            <div class="card-body">
                <p><strong>Formula:</strong> y<sub>ij</sub> = w<sub>j</sub> × r<sub>ij</sub></p>
                
                <h6 class="text-info">Bobot Kriteria:</h6>
                <div class="table-responsive mb-3">
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
                                <?php foreach ($step3['weights'] as $w): ?>
                                <td><strong><?= $w ?></strong></td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="text-info">Matriks Terbobot:</h6>
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
                                <td><?= number_format($step3['weightedMatrix'][$i][$j], 4) ?></td>
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
                <h5 class="mb-0">🎯 STEP 4: Solusi Ideal Positif & Negatif</h5>
            </div>
            <div class="card-body">
                <p><strong>Benefit:</strong> A<sup>+</sup> = max, A<sup>-</sup> = min</p>
                <p><strong>Cost:</strong> A<sup>+</sup> = min, A<sup>-</sup> = max</p>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Kriteria</th>
                                <th>Tipe</th>
                                <th>A<sup>+</sup> (Ideal Positif)</th>
                                <th>A<sup>-</sup> (Ideal Negatif)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($criteria as $j => $crit): ?>
                            <tr>
                                <td><strong><?= esc($crit) ?></strong></td>
                                <td>
                                    <span class="badge <?= $step4['criteriaTypes'][$j] === 'benefit' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ucfirst($step4['criteriaTypes'][$j]) ?>
                                    </span>
                                </td>
                                <td><span class="badge bg-primary"><?= number_format($step4['idealPositive'][$j], 4) ?></span></td>
                                <td><span class="badge bg-secondary"><?= number_format($step4['idealNegative'][$j], 4) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- STEP 5 -->
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">📏 STEP 5: Jarak ke Solusi Ideal</h5>
            </div>
            <div class="card-body">
                <p><strong>Formula:</strong> D<sup>+</sup> = √Σ(y<sub>ij</sub> - y<sub>j</sub><sup>+</sup>)²</p>
                <p><strong>Formula:</strong> D<sup>-</sup> = √Σ(y<sub>ij</sub> - y<sub>j</sub><sup>-</sup>)²</p>
                
                <?php foreach ($step5['distanceDetails'] as $altName => $details): ?>
                <div class="card mb-3 bg-dark">
                    <div class="card-header bg-secondary">
                        <h6 class="mb-0 text-white">Perhitungan: <strong><?= esc($altName) ?></strong></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Jarak ke Ideal Positif (D<sup>+</sup>):</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Kriteria</th>
                                                <th>Nilai</th>
                                                <th>Ideal</th>
                                                <th>(Nilai - Ideal)²</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($details['positive']['details'] as $d): ?>
                                            <tr>
                                                <td><?= esc($d['criteria']) ?></td>
                                                <td><?= number_format($d['value'], 4) ?></td>
                                                <td><?= number_format($d['ideal'], 4) ?></td>
                                                <td><?= number_format($d['squared'], 4) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-info">
                                            <tr>
                                                <th colspan="3">Σ =</th>
                                                <th><?= number_format($details['positive']['sum'], 4) ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="3">D<sup>+</sup> = √Σ =</th>
                                                <th><span class="badge bg-primary"><?= number_format($details['positive']['distance'], 4) ?></span></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-success">Jarak ke Ideal Negatif (D<sup>-</sup>):</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Kriteria</th>
                                                <th>Nilai</th>
                                                <th>Ideal</th>
                                                <th>(Nilai - Ideal)²</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($details['negative']['details'] as $d): ?>
                                            <tr>
                                                <td><?= esc($d['criteria']) ?></td>
                                                <td><?= number_format($d['value'], 4) ?></td>
                                                <td><?= number_format($d['ideal'], 4) ?></td>
                                                <td><?= number_format($d['squared'], 4) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-success">
                                            <tr>
                                                <th colspan="3">Σ =</th>
                                                <th><?= number_format($details['negative']['sum'], 4) ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="3">D<sup>-</sup> = √Σ =</th>
                                                <th><span class="badge bg-success"><?= number_format($details['negative']['distance'], 4) ?></span></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- STEP 6 -->
        <div class="card mb-4">
            <div class="card-header bg-purple text-white" style="background: linear-gradient(135deg, #7b2ff7 0%, #a05ff7 100%) !important;">
                <h5 class="mb-0">🎊 STEP 6: Preferensi Relatif (Nilai V)</h5>
            </div>
            <div class="card-body">
                <p><strong>Formula:</strong> V<sub>i</sub> = D<sup>-</sup> / (D<sup>-</sup> + D<sup>+</sup>)</p>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Alternatif</th>
                                <th>D<sup>+</sup></th>
                                <th>D<sup>-</sup></th>
                                <th>Perhitungan</th>
                                <th>Nilai V</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($step6['preferences'] as $pref): ?>
                            <tr>
                                <td><strong><?= esc($pref['name']) ?></strong></td>
                                <td><?= number_format($pref['dPlus'], 4) ?></td>
                                <td><?= number_format($pref['dMinus'], 4) ?></td>
                                <td><code><?= number_format($pref['dMinus'], 4) ?> / (<?= number_format($pref['dMinus'], 4) ?> + <?= number_format($pref['dPlus'], 4) ?>)</code></td>
                                <td><span class="badge bg-success"><?= number_format($pref['score'], 4) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Hasil Final -->
        <div class="card">
            <div class="card-header text-white" style="background: linear-gradient(135deg, #f72f85 0%, #ff6b9d 100%) !important;">
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

<div class="container">
    <h2 class="mb-4">📈 Metode TOPSIS</h2>
    
    <div class="alert alert-success">
        <strong>Cara Kerja:</strong> TOPSIS mencari alternatif yang paling dekat dengan solusi ideal positif dan paling jauh dari solusi ideal negatif.
    </div>

    <form method="post" action="<?= base_url('topsis/calculate/') ?>" id="topsisForm">
        <?= csrf_field() ?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
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

        <button type="submit" class="btn btn-success btn-lg w-100" id="calculateBtn" style="display:none;">
            Hitung Hasil TOPSIS
        </button>
    </form>
</div>

<script>
function generateInputs() {
    const numAlts = parseInt(document.getElementById('numAlternatives').value);
    const numCrits = parseInt(document.getElementById('numCriteria').value);
    
    let html = '<div class="card mb-4"><div class="card-header bg-secondary text-white"><h5 class="mb-0">2. Nama Alternatif</h5></div><div class="card-body"><div class="row">';
    
    for (let i = 0; i < numAlts; i++) {
        html += `<div class="col-md-4 mb-3">
            <label class="form-label">Alternatif ${i+1}:</label>
            <input type="text" name="alternatives[]" class="form-control" placeholder="Contoh: Smartphone A" required>
        </div>`;
    }
    html += '</div></div></div>';
    
    html += '<div class="card mb-4"><div class="card-header bg-secondary text-white"><h5 class="mb-0">3. Kriteria, Bobot, dan Tipe</h5></div><div class="card-body">';
    html += '<div class="table-responsive"><table class="table table-bordered"><thead class="table-light"><tr><th>Kriteria</th><th>Bobot</th><th>Tipe</th></tr></thead><tbody>';
    
    for (let i = 0; i < numCrits; i++) {
        html += `<tr>
            <td><input type="text" name="criteria[]" class="form-control" placeholder="Contoh: Harga" required></td>
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