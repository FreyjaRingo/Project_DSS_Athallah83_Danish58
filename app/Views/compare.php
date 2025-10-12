<div class="container">
    <h2 class="mb-4">🔄 Perbandingan Metode DSS</h2>
    
    <div class="alert alert-primary">
        <strong>💡 Fitur Perbandingan:</strong> Input data sekali, bandingkan hasil dari 2 metode berbeda untuk melihat konsistensi peringkat alternatif.
    </div>

    <form method="post" action="<?= base_url('compare/calculate') ?>" id="compareForm">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">1. Pilih Metode yang Akan Dibandingkan</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Metode Pertama:</label>
                        <select name="method1" class="form-select" required>
                            <option value="AHP">AHP - Analytical Hierarchy Process</option>
                            <option value="TOPSIS">TOPSIS - Technique for Order Preference</option>
                            <option value="SAW">SAW - Simple Additive Weighting</option>
                            <option value="WP">WP - Weighted Product</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Metode Kedua:</label>
                        <select name="method2" class="form-select" required>
                            <option value="TOPSIS">TOPSIS - Technique for Order Preference</option>
                            <option value="AHP">AHP - Analytical Hierarchy Process</option>
                            <option value="SAW">SAW - Simple Additive Weighting</option>
                            <option value="WP">WP - Weighted Product</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">2. Input Jumlah Data</h5>
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
            🔍 Bandingkan Metode
        </button>
    </form>

    <?php if (isset($results1) && isset($results2)): ?>
    <div class="result-card mt-5">
        <h3 class="mb-4">📊 Hasil Perbandingan</h3>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Metode <?= esc($method1) ?></h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($results1 as $index => $result): ?>
                        <div class="p-3 mb-2 rounded <?= $index < 3 ? 'rank-'.($index+1) : 'bg-light' ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">
                                        <span class="badge bg-dark me-2">#<?= $index + 1 ?></span>
                                        <?= esc($result['name']) ?>
                                    </h6>
                                </div>
                                <div>
                                    <strong><?= number_format($result['score'], 4) ?></strong>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Metode <?= esc($method2) ?></h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($results2 as $index => $result): ?>
                        <div class="p-3 mb-2 rounded <?= $index < 3 ? 'rank-'.($index+1) : 'bg-light' ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">
                                        <span class="badge bg-dark me-2">#<?= $index + 1 ?></span>
                                        <?= esc($result['name']) ?>
                                    </h6>
                                </div>
                                <div>
                                    <strong><?= number_format($result['score'], 4) ?></strong>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">📈 Analisis Perbandingan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Alternatif</th>
                                <th>Rank <?= esc($method1) ?></th>
                                <th>Rank <?= esc($method2) ?></th>
                                <th>Selisih</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comparison as $comp): ?>
                            <tr>
                                <td><strong><?= esc($comp['name']) ?></strong></td>
                                <td class="text-center">
                                    <span class="badge bg-primary">#<?= $comp['rank1'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">#<?= $comp['rank2'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $comp['difference'] ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($comp['status'] == 'same'): ?>
                                        <span class="badge bg-success">✓ Identik</span>
                                    <?php elseif ($comp['status'] == 'similar'): ?>
                                        <span class="badge bg-warning">≈ Mirip</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">✗ Berbeda</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <h6 class="fw-bold">Kesimpulan:</h6>
                    <?php
                        $sameCount = count(array_filter($comparison, fn($c) => $c['status'] == 'same'));
                        $similarCount = count(array_filter($comparison, fn($c) => $c['status'] == 'similar'));
                        $differentCount = count(array_filter($comparison, fn($c) => $c['status'] == 'different'));
                        $totalCount = count($comparison);
                    ?>
                    <ul class="list-unstyled">
                        <li>✅ <strong><?= $sameCount ?></strong> alternatif memiliki peringkat identik (<?= number_format($sameCount/$totalCount*100, 1) ?>%)</li>
                        <li>⚠️ <strong><?= $similarCount ?></strong> alternatif memiliki peringkat mirip/berdekatan (<?= number_format($similarCount/$totalCount*100, 1) ?>%)</li>
                        <li>❌ <strong><?= $differentCount ?></strong> alternatif memiliki peringkat berbeda signifikan (<?= number_format($differentCount/$totalCount*100, 1) ?>%)</li>
                    </ul>

                    <?php if ($sameCount >= $totalCount * 0.7): ?>
                        <div class="alert alert-success">
                            <strong>Konsistensi Tinggi!</strong> Kedua metode menghasilkan peringkat yang sangat konsisten (≥70% identik).
                        </div>
                    <?php elseif ($sameCount + $similarCount >= $totalCount * 0.7): ?>
                        <div class="alert alert-warning">
                            <strong>Konsistensi Sedang.</strong> Kedua metode menghasilkan peringkat yang cukup konsisten dengan beberapa perbedaan kecil.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <strong>Konsistensi Rendah.</strong> Kedua metode menghasilkan peringkat yang cukup berbeda. Pertimbangkan untuk meninjau kembali bobot dan tipe kriteria.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function generateInputs() {
    const numAlts = parseInt(document.getElementById('numAlternatives').value);
    const numCrits = parseInt(document.getElementById('numCriteria').value);
    
    let html = '<div class="card mb-4"><div class="card-header bg-secondary text-white"><h5 class="mb-0">3. Nama Alternatif</h5></div><div class="card-body"><div class="row">';
    
    for (let i = 0; i < numAlts; i++) {
        html += `<div class="col-md-4 mb-3">
            <label class="form-label">Alternatif ${i+1}:</label>
            <input type="text" name="alternatives[]" class="form-control" placeholder="Contoh: Produk A" required>
        </div>`;
    }
    html += '</div></div></div>';
    
    html += '<div class="card mb-4"><div class="card-header bg-secondary text-white"><h5 class="mb-0">4. Kriteria, Bobot, dan Tipe</h5></div><div class="card-body">';
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
    
    html += '<div class="card mb-4"><div class="card-header bg-info text-white"><h5 class="mb-0">5. Nilai Alternatif untuk Setiap Kriteria</h5></div><div class="card-body">';
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