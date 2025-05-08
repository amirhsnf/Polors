<?php class polors
{
    private array | null $light = null;
    private array $light_sr = [0, 15];
    private array $light_vr = [85, 100];
    private array | null $tertiary = null;
    private array | null $primary = null;
    private array | null $secondary = null;
    private array $main_sr = [10, 50];
    private array $main_vr = [30, 70];
    private array | null $dark = null;
    private array $dark_sr = [0, 100];
    private array $dark_vr = [0, 25];
    private array | null $success = null;
    private array $success_hr = [65, 150];
    private array $success_sr = [40, 100];
    private array $success_vr = [30, 100];
    private array | null $warning = null;
    private array $warning_hr = [20, 55];
    private array $warning_sr = [60, 100];
    private array $warning_vr = [85, 100];
    private array | null $danger = null;
    private array $danger_hr = [0, 10];
    private array $danger_sr = [75, 100];
    private array $danger_vr = [70, 100];
    public function generate_colors($colors)
    {
        if(count($colors) != 8) return false;
        $this->init($colors);
        $this->handle_primary();
        $this->handle_secondary();
        $this->handle_tertiary();
        $this->handle_light();
        $this->handle_dark();
        $this->handle_notice('success');
        $this->handle_notice('warning');
        $this->handle_notice('danger');
        return [
            $this->hsv_to_hex($this->light),
            $this->hsv_to_hex($this->tertiary),
            $this->hsv_to_hex($this->primary),
            $this->hsv_to_hex($this->secondary),
            $this->hsv_to_hex($this->dark),
            $this->hsv_to_hex($this->success),
            $this->hsv_to_hex($this->warning),
            $this->hsv_to_hex($this->danger)
        ];
    }
    private function init($colors){
        if($colors[0]) $this->light = $this->hex_to_hsv($colors[0]);
        if($colors[1]) $this->tertiary = $this->hex_to_hsv($colors[1]);
        if($colors[2]) $this->primary = $this->hex_to_hsv($colors[2]);
        if($colors[3]) $this->secondary = $this->hex_to_hsv($colors[3]);
        if($colors[4]) $this->dark = $this->hex_to_hsv($colors[4]);
        if($colors[5]) $this->success = $this->hex_to_hsv($colors[5]);
        if($colors[6]) $this->warning = $this->hex_to_hsv($colors[6]);
        if($colors[7]) $this->danger = $this->hex_to_hsv($colors[7]);
    }
    private function handle_primary(): void
    {
        if($this->primary)
            return;
        if($this->secondary){
            $generate = $this->get_complementary($this->secondary);
            $this->primary = $generate[0];
            if(!$this->tertiary)
                $this->tertiary = $generate[1];
            return;
        }
        if($this->tertiary){
            $generate = $this->get_complementary($this->tertiary);
            $this->primary = $generate[0];
            $this->secondary = $generate[1];
            return;
        }
        if($this->light){
            $generate = $this->get_complementary($this->light, $this->main_sr, $this->main_vr, true);
            $this->primary = $generate[0];
            return;
        }
        if($this->dark){
            $generate = $this->get_complementary($this->dark, $this->main_sr, $this->main_vr, true);
            $this->primary = $generate[0];
            return;
        }
        $generate = $this->get_complementary(null, $this->main_sr, $this->main_vr, true);
        $this->primary = $generate[0];
    }
    private function handle_secondary(): void
    {
        if($this->secondary)
            return;
        $generate = $this->get_complementary($this->primary);
        $this->secondary = $generate[0];
        if(!$this->tertiary)
            $this->tertiary = $generate[1];

    }
    private function handle_tertiary(): void
    {
        if($this->tertiary)
            return;
        $generate = $this->get_complementary($this->primary);
        $this->tertiary = $generate[0];
    }
    private function handle_light(): void
    {
        if($this->light)
            return;
        $generate = $this->get_complementary($this->primary, $this->light_sr, $this->light_vr, true);
        $this->light = $generate[0];
    }
    private function handle_dark(): void
    {
        if($this->dark)
            return;
        $generate = $this->get_complementary($this->primary, $this->dark_sr, $this->dark_vr, true);
        $this->dark = $generate[0];
    }
    private function handle_notice($notice): void
    {
        if($this->$notice)
            return;
        list($ph, $ps, $pv) = $this->primary;
        $notice_sr = $notice.'_sr';
        $ns = $this->normalize_sv($ps, $this->$notice_sr);
        $notice_vr = $notice.'_vr';
        $nv = $this->normalize_sv($pv, $this->$notice_vr);
        $notice_hr = $notice.'_hr';
        $notice_hr = $this->$notice_hr;
        $this->$notice = [
            rand($notice_hr[0], $notice_hr[1]),
            $this->shake_sv($ns),
            $this->shake_sv($nv),
        ];
    }
    private function normalize_sv($p, $t){
        if($p >= $t[0] && $p <= $t[1])
            return $p;
        if($p < $t[0]){
            return $t[0];
        }
        return $t[1];
    }
    private function get_complementary($hsv = null, $sr = null, $vr = null, $same_h = false)
    {
        if(!$hsv){
            $hsv = [rand(0, 359), rand(0,100), rand(0,100)];
        }
        list($h, $s, $v) = $hsv;
        $methods = [[30, 330], [60, 300], [150, 210], [30, 180], [180, 330], [30, 210], [150, 330]];
        $method = $methods[rand(0, count($methods) - 1)];
//        $method = $methods[6];
        shuffle($method);
        if($sr)
            $s = rand($sr[0], $sr[1]);
        if($vr)
            $v = rand($vr[0], $vr[1]);
        if($same_h)
            $method = [0, 0];
        return [
            [
                $this->change_hue($h, $method[0]),
                $this->shake_sv($s),
                $this->shake_sv($v)
            ],
            [
                $this->change_hue($h, $method[1]),
                $this->shake_sv($s),
                $this->shake_sv($v)
            ]
        ];
    }
    private function shake_sv($sv)
    {
        $sv += rand(-5, 5);
        if($sv > 100) $sv = 100;
        if($sv < 0) $sv = 0;
        return $sv;
    }
    public function change_hue($h, $value): int
    {
        $h += $value;
        $h += rand(-5, 5);
        $h = $h % 360;
        if($h < 0)
            $h = 360 - $h;
        return $h;
    }
    public function hex_to_hsv($hex): array
    {
        return $this->rgb_to_hsv($this->hex_to_rgb($hex));
    }
    public function hex_to_rgb($hex): array
    {
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        return [$r, $g, $b];
    }
    public function rgb_to_hsv($rgb): array
    {
        $r = $rgb[0] / 255;
        $g = $rgb[1] / 255;
        $b = $rgb[2] / 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $v = round(100 * $max);
        $d = $max - $min;
        if($d == 0){
            return [0, 0, $v];
        }
        $s = round(100 * ($d / $max));
        if($r == $min)
            $h = 3 - (($g - $b) / $d);
        elseif($b == $min)
            $h = 1 - (($r - $g) / $d);
        else
            $h = 5 - (($b - $r) / $d);
        $h = round(60 * $h);
        return [$h, $s, $v];
    }
    public function hsv_to_rgb($hsv): array
    {
        list($h, $s, $v) = $hsv;
        $rgb = [0, 0, 0];
        for($i = 0; $i < 4; $i++){
            if(abs($h - $i * 120) < 120){
                $d = max(60, abs($h - $i * 120));
                $rgb[$i % 3] = 1 - (($d - 60) / 60);
            }
        }
        $max = max($rgb);
        $factor = 255 * ($v / 100);
        for($i = 0; $i < 3; $i++){
            $rgb[$i] = round(($rgb[$i] + ($max - $rgb[$i]) * (1 - $s / 100)) * $factor);
        }
        return $rgb;
    }
    public function rgb_to_hex($rgb): string
    {
        return sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]);
    }
    public function hsv_to_hex($hsv): string
    {
        return $this->rgb_to_hex($this->hsv_to_rgb($hsv));
    }
}
