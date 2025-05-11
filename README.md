[![Live Demo](https://img.shields.io/badge/Live%20Demo-Polors-green?style=for-the-badge)](https://polors.pyno.ir)

![Screenshot](./assets/preview.png)

# Polors – PHP Color Generator
A simple PHP class to generate primary, secondary, and complementary colors based on given inputs. Useful for theming, UI design, and CMS integrations.

## Features
- Auto-generate color palette from a base color
- Support for RGB <=> HEX <=> HSV conversion
- Modular and extensible code

## Example Usage
```php
require 'src/polors.php';

$polor = new polors();
$palette = $polor->generate_colors(['#ffffff', null, null, '#3498db', null, '#2ecc71', '#f1c40f', '#e74c3c']);
print_r($palette);
