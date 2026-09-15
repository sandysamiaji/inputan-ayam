<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\WeeklyStandard;

class WeeklyStandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = <<<EOD
13	0	-	75	1,11	1,14	1,18
14	0	-	77,5	1,18	1,22	1,26
15	0	-	80	1,25	1,29	1,33
16	0	-	82,5	1,32	1,36	1,40
17	0	-	85	1,39	1,43	1,47
18	0	-	90	1,46	1,50	1,55
19	8,5	44,6	95	1,53	1,57	1,62
20	34,5	47,1	100	1,59	1,64	1,69
21	53,5	49,7	105	1,66	1,71	1,76
22	70,6	52,2	108	1,72	1,77	1,83
23	81,5	54,4	110	1,77	1,83	1,88
24	87,3	56,3	112	1,82	1,87	1,93
25	90,5	58,1	113	1,85	1,90	1,96
26	92,3	59,3	114	1,86	1,92	1,98
27	93,4	60,5	115	1,87	1,93	1,98
28	93,9	61,3	115	1,87	1,93	1,99
29	94,3	62,0	115	1,88	1,94	1,99
30	94,5	62,6	115	1,88	1,94	2,00
31	94,7	63,2	115	1,89	1,95	2,00
32	94,8	63,6	115	1,89	1,95	2,01
33	94,9	63,9	115	1,89	1,95	2,01
34	94,8	64,2	115	1,89	1,95	2,01
35	94,7	64,4	115	1,90	1,96	2,01
36	94,6	64,6	115	1,90	1,96	2,02
37	94,4	64,8	115	1,90	1,96	2,02
38	94,2	65,0	115	1,90	1,96	2,02
39	94,1	65,1	115	1,91	1,97	2,02
40	93,9	65,3	115	1,91	1,97	2,03
41	93,7	65,4	115	1,91	1,97	2,03
42	93,5	65,6	115	1,91	1,97	2,03
43	93,3	65,7	115	1,97	1,97	2,03
44	93,1	65,8	115	1,92	1,98	2,04
45	92,8	65,9	115	1,92	1,98	2,04
46	92,5	66,0	115	1,92	1,98	2,04
47	92,2	66,1	115	1,92	1,98	2,04
48	91,9	66,2	115	1,93	1,99	2,04
49	91,6	66,3	115	1,93	1,99	2,05
50	91,2	66,4	115	1,93	1,99	2,05
51	90,8	66,5	115	1,93	1,99	2,05
52	90,5	66,6	115	1,99	1,99	2,05
53	90,0	66,7	115	1,94	2,00	2,06
54	89,7	66,8	115	1,94	2,00	2,06
55	89,3	66,8	115	1,94	2,00	2,06
56	89,0	66,9	115	1,94	2,00	2,06
57	88,6	67,0	115	1,95	2,01	2,07
58	88,2	67,0	115	1,95	2,01	2,07
59	87,8	67,1	115	1,95	2,01	2,07
60	87,4	67,2	115	1,95	2,01	2,07
61	87,1	67,2	116	1,95	2,01	2,07
62	86,7	67,3	116	1,96	2,02	2,08
63	86,3	67,4	116	1,96	2,02	2,08
64	85,9	67,4	116	1,96	2,02	2,08
65	85,6	67,5	116	1,96	2,02	2,08
66	85,2	67,5	116	1,96	2,03	2,09
67	84,8	67,6	116	1,97	2,03	2,09
68	84,4	67,7	116	1,97	2,03	2,09
69	83,9	67,7	116	1,97	2,03	2,09
70	83,5	67,8	116	1,97	2,03	2,09
71	83,0	67,8	116	1,97	2,04	2,10
72	82,5	67,9	116	1,97	2,04	2,10
73	81,9	67,9	117	1,98	2,04	2,10
74	81,4	68,0	117	1,98	2,04	2,10
75	80,9	68,0	117	1,98	2,04	2,10
76	80,4	68,1	117	1,98	2,04	2,10
77	79,8	68,1	117	1,98	2,05	2,11
78	79,3	68,2	117	1,99	2,05	2,11
79	78,7	68,2	117	1,99	2,05	2,11
80	78,2	68,3	117	1,99	2,05	2,11
81	77,6	68,3	117	1,99	2,05	2,11
82	77,0	68,4	117	1,99	2,05	2,11
83	76,4	68,4	117	1,99	2,05	2,12
84	75,9	68,4	117	1,99	2,06	2,12
85	75,3	68,5	117	1,99	2,06	2,12
86	74,7	68,5	117	2,00	2,06	2,12
87	74,1	68,6	117	2,00	2,06	2,12
88	73,4	68,6	117	2,00	2,06	2,12
89	72,8	68,6	117	2,06	2,06	2,12
90	72,2	68,7	117	2,06	2,06	2,12
EOD;

        $lines = explode("\n", trim($data));
        
        WeeklyStandard::query()->delete();

        foreach ($lines as $line) {
            $cols = explode("\t", trim($line));
            if (count($cols) < 7) continue;

            $week = (int) $cols[0];
            
            // Parser angka (koma jadi titik)
            $parseNum = function($val) {
                $val = str_replace(',', '.', $val);
                return is_numeric($val) ? (float) $val : 0;
            };

            $hd = $parseNum($cols[1]);
            $eggWt = str_replace(',', '.', $cols[2]);
            $eggWt = $eggWt === '-' ? null : $eggWt;
            $feed = $parseNum($cols[3]);
            $bbMin = $parseNum($cols[4]);
            $bbTarget = $parseNum($cols[5]);
            $bbMax = $parseNum($cols[6]);

            // Default fase dari fallback logic ProductionStandardService (kira-kira)
            $fase = '';
            $pill = '';
            $feedType = '';
            $desc = '';

            if ($week >= 13 && $week <= 15) {
                $fase = 'Grower Akhir (Pra-Laying)';
                $pill = 'GROWER';
                $feedType = 'Grower / Pullet';
                $desc = 'Fokus pada pembentukan kerangka tubuh dan keseragaman bobot badan ayam.';
            } elseif ($week >= 16 && $week <= 17) {
                $fase = 'Persiapan Bertelur (Pre-Lay)';
                $pill = 'PRE-LAY';
                $feedType = 'Pre-Lay / Layer Awal';
                $desc = 'Fokus pada pembentukan kerangka tubuh dan keseragaman bobot badan ayam.';
            } elseif ($week >= 18 && $week <= 20) {
                $fase = 'Awal Bertelur (Puncak Naik)';
                $pill = 'AWAL BERTELUR';
                $feedType = 'Layer Phase 1';
                $desc = 'Ayam membutuhkan energi dan nutrisi tertinggi untuk pembentukan telur pertama.';
            } elseif ($week >= 21 && $week <= 40) {
                $fase = 'Puncak Produksi (Egg Peak)';
                $pill = 'PUNCAK PRODUKSI';
                $feedType = 'Layer Phase 1';
                $desc = 'Konsumsi pakan stabil. Energi tertinggi dibutuhkan untuk produksi telur maskimal.';
            } elseif ($week >= 41 && $week <= 60) {
                $fase = 'Laying Phase 2 (Pasca Puncak)';
                $pill = 'PASCA PUNCAK';
                $feedType = 'Layer Phase 2';
                $desc = 'Persentase bertelur mulai menurun, namun ukuran telur bertambah besar.';
            } else {
                $fase = 'Laying Phase 3 (Fase Akhir) / Afkir';
                $pill = 'FASE AKHIR';
                $feedType = 'Layer Phase 3';
                $desc = 'Fase akhir produksi sebelum peremajaan.';
            }

            WeeklyStandard::create([
                'week' => $week,
                'phase' => $fase,
                'pill' => $pill,
                'feed_type' => $feedType,
                'hd_target' => $hd,
                'egg_weight' => $eggWt,
                'feed_gram' => $feed,
                'weight_min' => $bbMin,
                'weight_target' => $bbTarget,
                'weight_max' => $bbMax,
                'description' => $desc,
            ]);
        }
    }
}
