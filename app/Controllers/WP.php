<?php

namespace App\Controllers;

use Exception;

class WP extends BaseController
{
    public function index()
    {
        return view('layout/header')
            . view('wp')
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

            // Validasi awal
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

            // STEP 1: Data Input
            $step1 = [
                'matrix' => $matrix,
                'alternatives' => $alternatives,
                'criteria' => $criteria,
                'criteriaTypes' => $criteriaTypes,
                'weights' => $weights
            ];

            // STEP 2: Normalisasi Bobot
            $totalWeight = array_sum(array_map('floatval', $weights));
            if ($totalWeight == 0) {
                throw new Exception("Total bobot tidak boleh nol.");
            }

            $normalizedWeights = [];
            $weightDetails = [];

            for ($j = 0; $j < $n; $j++) {
                $w = floatval($weights[$j]) / $totalWeight;
                $finalWeight = ($criteriaTypes[$j] === 'benefit') ? $w : -$w;

                $normalizedWeights[$j] = $finalWeight;
                $weightDetails[$criteria[$j]] = [
                    'original' => floatval($weights[$j]),
                    'normalized' => $w,
                    'type' => $criteriaTypes[$j],
                    'finalWeight' => $finalWeight
                ];
            }

            $step2 = [
                'totalWeight' => $totalWeight,
                'normalizedWeights' => $normalizedWeights,
                'weightDetails' => $weightDetails
            ];

            // STEP 3: Hitung Vektor S
            $vectorSDetails = [];
            $vectorS = [];

            for ($i = 0; $i < $m; $i++) {
                $s = 1;
                $details = [];

                for ($j = 0; $j < $n; $j++) {
                    if (!isset($matrix[$i][$j])) {
                        throw new Exception("Data matriks tidak lengkap pada alternatif {$alternatives[$i]}.");
                    }

                    $value = floatval($matrix[$i][$j]);
                    $power = $normalizedWeights[$j];
                    $result = pow($value, $power);

                    $details[] = [
                        'criteria' => $criteria[$j],
                        'value' => $value,
                        'weight' => $power,
                        'result' => $result
                    ];

                    $s *= $result;
                }

                $vectorS[$i] = $s;
                $vectorSDetails[$alternatives[$i]] = [
                    'details' => $details,
                    'vectorS' => $s
                ];
            }

            $step3 = [
                'vectorS' => $vectorS,
                'vectorSDetails' => $vectorSDetails
            ];

            // STEP 4: Hitung Vektor V
            $totalS = array_sum($vectorS);
            if ($totalS == 0) {
                throw new Exception("Total nilai vektor S tidak boleh nol.");
            }

            $vectorVDetails = [];
            $finalScores = [];

            for ($i = 0; $i < $m; $i++) {
                $v = $vectorS[$i] / $totalS;

                $vectorVDetails[$alternatives[$i]] = [
                    'vectorS' => $vectorS[$i],
                    'totalS' => $totalS,
                    'vectorV' => $v
                ];

                $finalScores[] = [
                    'name' => $alternatives[$i],
                    'score' => $v,
                    'vectorS' => $vectorS[$i]
                ];
            }

            $step4 = [
                'totalS' => $totalS,
                'vectorVDetails' => $vectorVDetails
            ];

            // Urutkan hasil akhir
            usort($finalScores, function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });

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
                . view('wp', $data)
                . view('layout/footer');
        } catch (Exception $e) {
            // Jika error terjadi, tampilkan halaman error sederhana
            $data = [
                'error' => $e->getMessage()
            ];

            return view('layout/header')
                . view('wp', $data)
                . view('layout/footer');
        }
    }
}
