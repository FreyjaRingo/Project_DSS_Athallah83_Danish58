<?php

namespace App\Controllers;

use Exception;

class SAW extends BaseController
{
    public function index()
    {
        return view('layout/header')
            . view('saw')
            . view('layout/footer');
    }

    public function calculate()
    {
        try {
            // Ambil data dari form
            $alternatives = $this->request->getPost('alternatives');
            $criteria = $this->request->getPost('criteria');
            $weights = $this->request->getPost('weights');
            $criteriaTypes = $this->request->getPost('criteria_types');
            $matrix = $this->request->getPost('matrix');

            // Validasi input awal
            if (
                empty($alternatives) || empty($criteria) ||
                empty($weights) || empty($criteriaTypes) || empty($matrix)
            ) {
                throw new Exception("Input tidak lengkap. Pastikan semua field sudah diisi.");
            }

            $m = count($alternatives);
            $n = count($criteria);

            if ($n !== count($weights) || $n !== count($criteriaTypes)) {
                throw new Exception("Jumlah kriteria, bobot, dan tipe kriteria tidak sesuai.");
            }

            // Validasi bobot (tidak boleh nol atau negatif)
            foreach ($weights as $idx => $w) {
                if (floatval($w) <= 0) {
                    throw new Exception("Bobot untuk kriteria '{$criteria[$idx]}' tidak boleh 0 atau negatif.");
                }
            }

            // STEP 1: Matriks Keputusan
            $step1 = [
                'matrix' => $matrix,
                'alternatives' => $alternatives,
                'criteria' => $criteria,
                'criteriaTypes' => $criteriaTypes,
                'weights' => $weights
            ];

            // STEP 2: Tentukan Max/Min per Kriteria
            $maxMinValues = [];
            for ($j = 0; $j < $n; $j++) {
                $column = array_column($matrix, $j);
                $column = array_map('floatval', $column);

                if (count(array_filter($column, fn($x) => $x != 0)) == 0) {
                    throw new Exception("Kriteria '{$criteria[$j]}' hanya memiliki nilai 0. Tidak dapat dinormalisasi.");
                }

                $maxMinValues[$j] = [
                    'max' => max($column),
                    'min' => min($column),
                    'type' => strtolower($criteriaTypes[$j])
                ];
            }

            $step2 = ['maxMinValues' => $maxMinValues];

            // STEP 3: Normalisasi Matriks
            $normalizedMatrix = [];
            $normalizationDetails = [];

            for ($i = 0; $i < $m; $i++) {
                $normalizationDetails[$alternatives[$i]] = [];

                for ($j = 0; $j < $n; $j++) {
                    $value = floatval($matrix[$i][$j]);
                    $type = $maxMinValues[$j]['type'];
                    $maxVal = $maxMinValues[$j]['max'];
                    $minVal = $maxMinValues[$j]['min'];

                    // Error jika ada pembagi nol
                    if ($type === 'benefit' && $maxVal == 0) {
                        throw new Exception("Nilai maksimum untuk kriteria '{$criteria[$j]}' adalah 0. Tidak bisa dinormalisasi.");
                    }
                    if ($type === 'cost' && $value == 0) {
                        throw new Exception("Nilai 0 terdeteksi pada kriteria '{$criteria[$j]}' (cost). Tidak bisa dilakukan pembagian.");
                    }

                    // Normalisasi berdasarkan tipe kriteria
                    if ($type === 'benefit') {
                        $normalized = $value / $maxVal;
                        $formula = "$value / $maxVal";
                    } elseif ($type === 'cost') {
                        $normalized = $minVal / $value;
                        $formula = "$minVal / $value";
                    } else {
                        throw new Exception("Tipe kriteria '{$criteria[$j]}' tidak valid. Gunakan 'benefit' atau 'cost'.");
                    }

                    // Cegah NaN atau INF
                    if (!is_finite($normalized)) {
                        throw new Exception("Terjadi kesalahan matematis pada normalisasi alternatif '{$alternatives[$i]}' di kriteria '{$criteria[$j]}'.");
                    }

                    $normalizedMatrix[$i][$j] = $normalized;
                    $normalizationDetails[$alternatives[$i]][$criteria[$j]] = [
                        'original' => $value,
                        'formula' => $formula,
                        'normalized' => $normalized
                    ];
                }
            }

            $step3 = [
                'normalizedMatrix' => $normalizedMatrix,
                'normalizationDetails' => $normalizationDetails
            ];

            // STEP 4: Perhitungan Skor (Preferensi)
            $scoreDetails = [];
            $finalScores = [];

            for ($i = 0; $i < $m; $i++) {
                $score = 0;
                $details = [];

                for ($j = 0; $j < $n; $j++) {
                    $weight = floatval($weights[$j]);
                    $normalized = $normalizedMatrix[$i][$j];
                    $contribution = $normalized * $weight;

                    // Cek validitas nilai
                    if (!is_finite($contribution)) {
                        throw new Exception("Nilai tidak valid terdeteksi saat menghitung skor untuk alternatif '{$alternatives[$i]}' pada kriteria '{$criteria[$j]}'.");
                    }

                    $details[] = [
                        'criteria' => $criteria[$j],
                        'normalized' => $normalized,
                        'weight' => $weight,
                        'contribution' => $contribution
                    ];

                    $score += $contribution;
                }

                $scoreDetails[$alternatives[$i]] = [
                    'details' => $details,
                    'totalScore' => $score
                ];

                $finalScores[] = [
                    'name' => $alternatives[$i],
                    'score' => $score
                ];
            }

            $step4 = ['scoreDetails' => $scoreDetails];

            // Urutkan hasil akhir
            usort($finalScores, fn($a, $b) => $b['score'] <=> $a['score']);

            $data = [
                'alternatives' => $alternatives,
                'criteria' => $criteria,
                'step1' => $step1,
                'step2' => $step2,
                'step3' => $step3,
                'step4' => $step4,
                'results' => $finalScores
            ];

            return view('layout/header')
                . view('saw', $data)
                . view('layout/footer');
        } catch (Exception $e) {
            // Log error ke file log CodeIgniter
            log_message('error', '[SAW ERROR] ' . $e->getMessage());

            // Kirim pesan error ke view
            $data = [
                'error' => $e->getMessage()
            ];

            return view('layout/header')
                . view('saw', $data)
                . view('layout/footer');
        }
    }
}
