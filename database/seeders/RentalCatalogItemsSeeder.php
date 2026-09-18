<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RentalCatalogItemsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Asegurar usuario Administrador CMS
        User::updateOrCreate(
            ['email' => 'admin@eventos.com'],
            [
                'name' => 'Administrador CMS',
                'password' => Hash::make('admin123456'),
                'email_verified_at' => now(),
            ]
        );

        Storage::disk('public')->makeDirectory('media');

        // Función auxiliar para generar banners SVG ilustrativos con gradientes y tipografía moderna
        $createSvgGraphic = function (string $title, string $category, string $bgStart, string $bgEnd, string $iconSvg) {
            $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
            $safeCategory = htmlspecialchars($category, ENT_QUOTES, 'UTF-8');
            return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" width="600" height="400">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$bgStart}" />
      <stop offset="100%" stop-color="{$bgEnd}" />
    </linearGradient>
    <filter id="shadow" x="-10%" y="-10%" width="120%" height="120%">
      <feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#000000" flood-opacity="0.35"/>
    </filter>
  </defs>
  <rect width="600" height="400" fill="url(#grad)" />
  <circle cx="530" cy="70" r="140" fill="#ffffff" fill-opacity="0.06" />
  <circle cx="70" cy="340" r="120" fill="#ffffff" fill-opacity="0.04" />
  
  <g transform="translate(300, 155)" filter="url(#shadow)" text-anchor="middle">
    {$iconSvg}
  </g>

  <rect x="20" y="295" width="560" height="85" rx="14" fill="#0f172a" fill-opacity="0.65" />
  <text x="300" y="332" text-anchor="middle" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="22" font-weight="700" fill="#ffffff">{$safeTitle}</text>
  <text x="300" y="358" text-anchor="middle" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="14" font-weight="600" fill="#38bdf8" letter-spacing="1">{$safeCategory}</text>
</svg>
SVG;
        };

        // 2. Definición de los 19 artículos solicitados para alquiler
        $itemsCatalog = [
            [
                'code' => 'CRP-001',
                'name' => 'Carpas 2x2',
                'category' => 'Carpas y Toldos',
                'description' => 'Carpa plegable impermeable de 2x2 metros con lona de alta densidad anti-desgarro y estructura reforzada de acero galvanizado. Incluye estacas de anclaje. Ideal para ferias, stands, puntos de control o eventos al aire libre.',
                'total_quantity' => 10,
                'daily_rate' => 45000.00,
                'weekend_rate' => 70000.00,
                'bg_start' => '#0284c7',
                'bg_end' => '#0369a1',
                'icon' => '<path d="M-40,30 L0,-45 L40,30 Z M-40,30 L-70,30 L-10,-45 Z M40,30 L70,30 L10,-45 Z" fill="#e0f2fe" /><rect x="-5" y="30" width="10" height="25" fill="#bae6fd" />',
            ],
            [
                'code' => 'CRP-002',
                'name' => 'Carpas 4x4',
                'category' => 'Carpas y Toldos',
                'description' => 'Carpa modular de 4x4 metros para 16 personas sentadas. Lona impermeable anti-rayos UV con opción de paredes laterales transparentes. Apta para recepciones, zonas de banquete y aniversarios.',
                'total_quantity' => 8,
                'daily_rate' => 90000.00,
                'weekend_rate' => 140000.00,
                'bg_start' => '#0369a1',
                'bg_end' => '#075985',
                'icon' => '<path d="M-60,35 L0,-50 L60,35 Z M-60,35 L-90,35 L-20,-50 Z M60,35 L90,35 L20,-50 Z" fill="#e0f2fe" /><line x1="-50" y1="35" x2="-50" y2="60" stroke="#bae6fd" stroke-width="6" /><line x1="50" y1="35" x2="50" y2="60" stroke="#bae6fd" stroke-width="6" />',
            ],
            [
                'code' => 'CRP-003',
                'name' => 'Carpa tipo hangar 20x8',
                'category' => 'Carpas y Toldos',
                'description' => 'Carpa tipo hangar monumental de 20x8 metros (160 m²) sin postes centrales intermedios. Estructura de aluminio aeroespacial y lona blackout ignífuga. Capacidad para más de 180 personas en eventos masivos o ferias comerciales.',
                'total_quantity' => 2,
                'daily_rate' => 1800000.00,
                'weekend_rate' => 2800000.00,
                'bg_start' => '#0c4a6e',
                'bg_end' => '#1e293b',
                'icon' => '<path d="M-90,40 C-90,-50 90,-50 90,40 Z" fill="none" stroke="#38bdf8" stroke-width="8" /><line x1="-90" y1="40" x2="90" y2="40" stroke="#bae6fd" stroke-width="4" /><path d="M-80,40 C-80,-30 80,-30 80,40" fill="none" stroke="#bae6fd" stroke-dasharray="8 8" stroke-width="3" />',
            ],
            [
                'code' => 'MOB-001',
                'name' => 'Sillas plásticas blancas',
                'category' => 'Mobiliario',
                'description' => 'Sillas ergonómicas color blanco sin brazos en polipropileno virgen de alta densidad. Apilables, ligeras y resistentes a más de 120 kg. Ideales para conferencias, bodas, grados y celebraciones masivas.',
                'total_quantity' => 150,
                'daily_rate' => 2500.00,
                'weekend_rate' => 3800.00,
                'bg_start' => '#475569',
                'bg_end' => '#334155',
                'icon' => '<rect x="-25" y="-45" width="50" height="40" rx="8" fill="#f8fafc" /><rect x="-25" y="-5" width="50" height="12" rx="4" fill="#e2e8f0" /><line x1="-20" y1="7" x2="-22" y2="45" stroke="#f8fafc" stroke-width="6" stroke-linecap="round" /><line x1="20" y1="7" x2="22" y2="45" stroke="#f8fafc" stroke-width="6" stroke-linecap="round" />',
            ],
            [
                'code' => 'MOB-002',
                'name' => 'Mesas para 4 personas plásticas',
                'category' => 'Mobiliario',
                'description' => 'Mesa cuadrada plástica de 80x80 cm con patas encajables reforzadas. Fácilmente combinable con manteles o forros. Perfecta para áreas de comida, terrazas y zonas auxiliares.',
                'total_quantity' => 35,
                'daily_rate' => 15000.00,
                'weekend_rate' => 22000.00,
                'bg_start' => '#3b82f6',
                'bg_end' => '#1d4ed8',
                'icon' => '<rect x="-60" y="-30" width="120" height="14" rx="5" fill="#f8fafc" /><line x1="-45" y1="-16" x2="-45" y2="45" stroke="#93c5fd" stroke-width="6" /><line x1="45" y1="-16" x2="45" y2="45" stroke="#93c5fd" stroke-width="6" /><line x1="-30" y1="-16" x2="-10" y2="45" stroke="#bfdbfe" stroke-width="4" stroke-linecap="round" /><line x1="30" y1="-16" x2="10" y2="45" stroke="#bfdbfe" stroke-width="4" stroke-linecap="round" />',
            ],
            [
                'code' => 'MOB-003',
                'name' => 'Mesones para 8 personas de madera',
                'category' => 'Mobiliario',
                'description' => 'Mesón rectangular de 2.40 x 0.80 m fabricado en madera pino selecta con acabado rústico barnizado. Patas plegables de acero con bloqueo de seguridad. Capacidad para 8 comensales.',
                'total_quantity' => 20,
                'daily_rate' => 35000.00,
                'weekend_rate' => 50000.00,
                'bg_start' => '#b45309',
                'bg_end' => '#78350f',
                'icon' => '<rect x="-80" y="-30" width="160" height="18" rx="4" fill="#fde68a" stroke="#d97706" stroke-width="3" /><line x1="-65" y1="-12" x2="-65" y2="45" stroke="#78350f" stroke-width="8" stroke-linecap="round" /><line x1="65" y1="-12" x2="65" y2="45" stroke="#78350f" stroke-width="8" stroke-linecap="round" /><line x1="-70" y1="20" x2="70" y2="20" stroke="#d97706" stroke-width="4" />',
            ],
            [
                'code' => 'ILU-003',
                'name' => 'Guirnaldas de luces',
                'category' => 'Iluminación',
                'description' => 'Guirnalda de bombillos vintage estilo feria / kermesse de 15 metros de largo con 25 soquetes E27 e iluminación cálida LED dimerizable. Resistente a lluvia (IP65) para ambientar techos, árboles o terrazas.',
                'total_quantity' => 25,
                'daily_rate' => 30000.00,
                'weekend_rate' => 45000.00,
                'bg_start' => '#eab308',
                'bg_end' => '#a16207',
                'icon' => '<path d="M-80,-20 Q-40,10 0,-10 Q40,10 80,-20" fill="none" stroke="#fef08a" stroke-width="5" /><circle cx="-40" cy="15" r="12" fill="#facc15" /><circle cx="0" cy="5" r="12" fill="#facc15" /><circle cx="40" cy="15" r="12" fill="#facc15" />',
            ],
            [
                'code' => 'AUD-003',
                'name' => 'Bafles',
                'category' => 'Audio',
                'description' => 'Bafles profesionales autoamplificados de 12 pulgadas 1000W RMS con conexión Bluetooth, ecualizador paramétrico y módulo DSP para voz y música en vivo. Incluye trípode y cableado.',
                'total_quantity' => 12,
                'daily_rate' => 80000.00,
                'weekend_rate' => 120000.00,
                'bg_start' => '#4338ca',
                'bg_end' => '#312e81',
                'icon' => '<rect x="-35" y="-55" width="70" height="110" rx="10" fill="#1e1b4b" stroke="#818cf8" stroke-width="4" /><circle cx="0" cy="-25" r="14" fill="#312e81" stroke="#a5b4fc" stroke-width="3" /><circle cx="0" cy="22" r="24" fill="#1e1b4b" stroke="#a5b4fc" stroke-width="4" /><circle cx="0" cy="22" r="8" fill="#818cf8" />',
            ],
            [
                'code' => 'HER-001',
                'name' => 'Guadaña',
                'category' => 'Herramientas y Maquinaria',
                'description' => 'Guadañadora desbrozadora a gasolina de 52cc de alto torque con arnés ergonómico y cuchilla triple de corte. Indispensable para adecuación de fincas, jardines y predios previo al montaje del evento.',
                'total_quantity' => 4,
                'daily_rate' => 50000.00,
                'weekend_rate' => 75000.00,
                'bg_start' => '#059669',
                'bg_end' => '#064e3b',
                'icon' => '<line x1="-60" y1="45" x2="50" y2="-45" stroke="#fcd34d" stroke-width="8" stroke-linecap="round" /><rect x="-70" y="30" width="28" height="28" rx="6" fill="#ef4444" /><ellipse cx="55" cy="-45" rx="26" ry="12" fill="#94a3b8" stroke="#f8fafc" stroke-width="3" transform="rotate(-30 55 -45)" />',
            ],
            [
                'code' => 'HER-002',
                'name' => 'Taladros',
                'category' => 'Herramientas y Maquinaria',
                'description' => 'Taladro percutor industrial 750W con mandril metálico de 1/2 pulgada, velocidad variable y estuche con brocas de concreto, metal y madera. Excelente para anclaje de estructuras y tarimas.',
                'total_quantity' => 6,
                'daily_rate' => 35000.00,
                'weekend_rate' => 50000.00,
                'bg_start' => '#ea580c',
                'bg_end' => '#9a3412',
                'icon' => '<rect x="-40" y="-30" width="65" height="35" rx="6" fill="#fed7aa" stroke="#c2410c" stroke-width="4" /><rect x="-25" y="5" width="22" height="40" rx="4" fill="#7c2d12" /><line x1="25" y1="-12" x2="60" y2="-12" stroke="#cbd5e1" stroke-width="6" stroke-linecap="round" />',
            ],
            [
                'code' => 'DEC-001',
                'name' => 'Letreros decorativos',
                'category' => 'Mantelería y Decoración',
                'description' => 'Letreros luminosos LED Neón Flex y madera calada con frases decorativas para bodas y recepciones ("Better Together", "Mis 15", "Love", "Bienvenidos"). Incluye transformador y base.',
                'total_quantity' => 8,
                'daily_rate' => 40000.00,
                'weekend_rate' => 60000.00,
                'bg_start' => '#db2777',
                'bg_end' => '#831843',
                'icon' => '<rect x="-65" y="-35" width="130" height="70" rx="14" fill="#500724" stroke="#f472b6" stroke-width="4" /><text x="0" y="8" font-family="Brush Script MT, cursive, sans-serif" font-size="30" fill="#fbcfe8" font-weight="bold" text-anchor="middle">Celebración</text>',
            ],
            [
                'code' => 'DEC-002',
                'name' => 'Bases para torta metálicas de un metro',
                'category' => 'Mantelería y Decoración',
                'description' => 'Base pedestal cilíndrica de 1 metro de altura con plato superior de 35 cm acabado en pintura electrostática dorada u oro rosa brillante. Resiste tortas de hasta 25 kg con máxima estabilidad.',
                'total_quantity' => 10,
                'daily_rate' => 30000.00,
                'weekend_rate' => 45000.00,
                'bg_start' => '#d97706',
                'bg_end' => '#92400e',
                'icon' => '<ellipse cx="0" cy="-35" rx="50" ry="12" fill="#fef3c7" stroke="#f59e0b" stroke-width="3" /><line x1="0" y1="-23" x2="0" y2="40" stroke="#fde68a" stroke-width="12" stroke-linecap="round" /><ellipse cx="0" cy="45" rx="40" ry="10" fill="#fef3c7" stroke="#f59e0b" stroke-width="3" />',
            ],
            [
                'code' => 'HER-003',
                'name' => 'Hidrolavadora',
                'category' => 'Herramientas y Maquinaria',
                'description' => 'Hidrolavadora de agua a presión 2200 PSI con motor eléctrico de inducción, manguera reforzada de 8 metros y lanza con boquilla turbo. Perfecta para lavado de lonas de carpas, tarimas y pisos.',
                'total_quantity' => 5,
                'daily_rate' => 60000.00,
                'weekend_rate' => 90000.00,
                'bg_start' => '#0284c7',
                'bg_end' => '#0f172a',
                'icon' => '<rect x="-30" y="-35" width="60" height="75" rx="10" fill="#38bdf8" /><circle cx="-25" cy="45" r="14" fill="#334155" /><circle cx="25" cy="45" r="14" fill="#334155" /><line x1="15" y1="-20" x2="55" y2="-45" stroke="#cbd5e1" stroke-width="6" stroke-linecap="round" />',
            ],
            [
                'code' => 'CMP-001',
                'name' => 'Carpings',
                'category' => 'Camping y Viajes',
                'description' => 'Carpa de camping iglú profesional para 4 personas con doble techo impermeable de 3000 mm de columna de agua, piso sellado y vestíbulo frontal. Incluye bolsa de transporte ligera.',
                'total_quantity' => 15,
                'daily_rate' => 35000.00,
                'weekend_rate' => 55000.00,
                'bg_start' => '#16a34a',
                'bg_end' => '#14532d',
                'icon' => '<path d="M-60,40 L0,-40 L60,40 Z" fill="#bbf7d0" stroke="#15803d" stroke-width="4" /><path d="M0,-40 L0,40" stroke="#166534" stroke-width="4" /><path d="M-20,40 L0,0 L20,40 Z" fill="#86efac" />',
            ],
            [
                'code' => 'VIA-001',
                'name' => 'Maletas para viajes',
                'category' => 'Camping y Viajes',
                'description' => 'Set de maletas de viaje rígidas ultrarresistentes de policarbonato con 4 ruedas dobles spinner 360°, asa telescópica de aluminio y candado de combinación TSA integrado.',
                'total_quantity' => 12,
                'daily_rate' => 25000.00,
                'weekend_rate' => 40000.00,
                'bg_start' => '#6366f1',
                'bg_end' => '#4338ca',
                'icon' => '<rect x="-35" y="-30" width="70" height="75" rx="8" fill="#e0e7ff" stroke="#4f46e5" stroke-width="4" /><line x1="-35" y1="-5" x2="35" y2="-5" stroke="#818cf8" stroke-width="3" /><line x1="-35" y1="18" x2="35" y2="18" stroke="#818cf8" stroke-width="3" /><rect x="-15" y="-50" width="30" height="20" rx="3" fill="none" stroke="#312e81" stroke-width="5" />',
            ],
            [
                'code' => 'TEC-001',
                'name' => 'Starlink',
                'category' => 'Tecnología',
                'description' => 'Kit satelital Starlink Standard de alta velocidad (150 a 250 Mbps) con router Wi-Fi 6 de cobertura amplia. Proporciona internet de banda ancha inmediato en eventos campestres, fincas o zonas sin señal.',
                'total_quantity' => 4,
                'daily_rate' => 160000.00,
                'weekend_rate' => 250000.00,
                'bg_start' => '#0f172a',
                'bg_end' => '#1e293b',
                'icon' => '<rect x="-40" y="-35" width="80" height="55" rx="6" fill="#f8fafc" stroke="#38bdf8" stroke-width="3" transform="rotate(-15)" /><line x1="0" y1="10" x2="0" y2="45" stroke="#94a3b8" stroke-width="6" /><line x1="-25" y1="45" x2="25" y2="45" stroke="#cbd5e1" stroke-width="8" stroke-linecap="round" /><path d="M-30,-45 C-15,-60 15,-60 30,-45" fill="none" stroke="#38bdf8" stroke-width="3" />',
            ],
            [
                'code' => 'MOB-004',
                'name' => 'Mesa madera',
                'category' => 'Mobiliario',
                'description' => 'Mesa cuadrada rústica de centro o banquete fabricada en madera maciza pulida de 1.20 x 0.80 m con tratamiento antidesgaste. Excelente protagonismo visual para salas lounge y zonas de cóctel.',
                'total_quantity' => 14,
                'daily_rate' => 40000.00,
                'weekend_rate' => 60000.00,
                'bg_start' => '#854d0e',
                'bg_end' => '#583101',
                'icon' => '<rect x="-60" y="-25" width="120" height="16" rx="4" fill="#fef08a" stroke="#ca8a04" stroke-width="3" /><line x1="-48" y1="-9" x2="-48" y2="45" stroke="#583101" stroke-width="8" stroke-linecap="round" /><line x1="48" y1="-9" x2="48" y2="45" stroke="#583101" stroke-width="8" stroke-linecap="round" />',
            ],
            [
                'code' => 'MOB-005',
                'name' => 'Silla quinceañera',
                'category' => 'Mobiliario',
                'description' => 'Trono imperial de Quinceañera tapizado en fino terciopelo blanco perla con espaldar alto capitoneado y tallado barroco en tono plateado. La pieza central para fotos y la mesa de la quinceañera.',
                'total_quantity' => 4,
                'daily_rate' => 90000.00,
                'weekend_rate' => 140000.00,
                'bg_start' => '#9333ea',
                'bg_end' => '#581c87',
                'icon' => '<path d="M-25,10 C-30,-50 30,-50 25,10 Z" fill="#f3e8ff" stroke="#c084fc" stroke-width="4" /><rect x="-28" y="10" width="56" height="12" rx="4" fill="#e9d5ff" /><line x1="-22" y1="22" x2="-22" y2="48" stroke="#a855f7" stroke-width="6" stroke-linecap="round" /><line x1="22" y1="22" x2="22" y2="48" stroke="#a855f7" stroke-width="6" stroke-linecap="round" /><circle cx="0" cy="-25" r="4" fill="#a855f7" />',
            ],
            [
                'code' => 'DEC-003',
                'name' => 'Manteles elegantes',
                'category' => 'Mantelería y Decoración',
                'description' => 'Manteles de gala de caída amplia en tela jacquard y lino pesado antimanchas para mesas redondas o rectangulares. Incluye caminos de mesa en brocado dorado, marfil o azul noche.',
                'total_quantity' => 80,
                'daily_rate' => 12000.00,
                'weekend_rate' => 18000.00,
                'bg_start' => '#0d9488',
                'bg_end' => '#134e4a',
                'icon' => '<path d="M-55,-20 L55,-20 L65,35 L-65,35 Z" fill="#ccfbf1" stroke="#2dd4bf" stroke-width="3" /><line x1="-65" y1="35" x2="65" y2="35" stroke="#14b8a6" stroke-width="5" /><line x1="-15" y1="-20" x2="-20" y2="35" stroke="#5eead4" stroke-width="2" stroke-dasharray="4 4" /><line x1="15" y1="-20" x2="20" y2="35" stroke="#5eead4" stroke-width="2" stroke-dasharray="4 4" />',
            ],
        ];

        // 3. Crear archivos de medios y registros en base de datos
        foreach ($itemsCatalog as $itemData) {
            $fileName = 'media/' . strtolower(str_replace('-', '_', $itemData['code'])) . '.svg';
            $svgContent = $createSvgGraphic(
                $itemData['name'],
                $itemData['category'],
                $itemData['bg_start'],
                $itemData['bg_end'],
                $itemData['icon']
            );

            Storage::disk('public')->put($fileName, $svgContent);

            $media = Media::updateOrCreate(
                ['path' => $fileName],
                [
                    'name' => $itemData['name'] . ' (' . $itemData['code'] . ')',
                    'mime_type' => 'image/svg+xml',
                    'size' => strlen($svgContent),
                    'active' => true,
                ]
            );

            Item::updateOrCreate(
                ['code' => $itemData['code']],
                [
                    'name' => $itemData['name'],
                    'category' => $itemData['category'],
                    'description' => $itemData['description'],
                    'total_quantity' => $itemData['total_quantity'],
                    'daily_rate' => $itemData['daily_rate'],
                    'weekend_rate' => $itemData['weekend_rate'],
                    'media_id' => $media->id,
                    'status' => 'disponible',
                ]
            );
        }
    }
}
