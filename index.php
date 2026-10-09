<?php

declare(strict_types=1);

$rdfFile = __DIR__ . DIRECTORY_SEPARATOR . 'Pariwisata_Kota_Yogyakarta.rdf';
$rdfNamespace = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
$propertyNamespace = 'https://wisatayogyakarta.neoverse.my.id/vocabulary#';
$baseUri = 'https://wisatayogyakarta.neoverse.my.id/';

/**
 * @return array{resources: array<string, array<string, list<string>>>, labels: array<string, string>, propertyLabels: array<string, string>}
 */
function loadRdf(string $file, string $rdfNamespace, string $propertyNamespace): array
{
    if (!is_readable($file)) {
        throw new RuntimeException('File RDF tidak dapat dibaca.');
    }

    libxml_use_internal_errors(true);
    $document = new DOMDocument();
    if (!$document->load($file)) {
        $errors = libxml_get_errors();
        libxml_clear_errors();
        $message = $errors[0]->message ?? 'Format RDF tidak valid.';
        throw new RuntimeException(trim($message));
    }
    libxml_clear_errors();

    $xpath = new DOMXPath($document);
    $xpath->registerNamespace('rdf', $rdfNamespace);
    $xpath->registerNamespace('prop', $propertyNamespace);

    /** @var array<string, array<string, list<string>>> $resources */
    $resources = [];
    /** @var array<string, string> $labels */
    $labels = [];
    /** @var array<string, string> $propertyLabels */
    $propertyLabels = [];

    foreach ($xpath->query('//rdf:Description') as $description) {
        if (!$description instanceof DOMElement) {
            continue;
        }

        $subject = $description->getAttributeNS($rdfNamespace, 'about');
        if ($subject === '') {
            continue;
        }
        $resources[$subject] = [];

        foreach ($description->childNodes as $property) {
            if (!$property instanceof DOMElement) {
                continue;
            }

            $predicate = $property->namespaceURI . $property->localName;
            $propertyLabel = $property->localName;
            $propertyLabels[$predicate] = $propertyLabel;

            if ($property->hasAttributeNS($rdfNamespace, 'resource')) {
                $value = $property->getAttributeNS($rdfNamespace, 'resource');
            } else {
                $value = trim($property->textContent);
            }

            if ($value === '') {
                continue;
            }
            $resources[$subject][$predicate][] = $value;

            if ($propertyLabel === 'memilikiNama' && !isset($labels[$subject])) {
                $labels[$subject] = $value;
            }
        }
    }

    return [
        'resources' => $resources,
        'labels' => $labels,
        'propertyLabels' => $propertyLabels,
    ];
}

function displayLabel(string $uri, array $labels): string
{
    if (isset($labels[$uri])) {
        return $labels[$uri];
    }

    $path = parse_url($uri, PHP_URL_PATH);
    $slug = is_string($path) ? basename(rtrim($path, '/')) : $uri;
    return ucwords(str_replace(['-', '_'], ' ', $slug));
}

function localUriLink(string $uri): string
{
    $path = parse_url($uri, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        return 'index.php?uri=' . rawurlencode($uri);
    }

    return rtrim($path, '/') . '/';
}

function propertyLabel(string $predicate): string
{
    $name = basename(str_replace('#', '/', $predicate));
    $labels = [
        'memilikiNama' => 'Nama Tempat',
        'memilikiGambar' => 'Gambar',
        'memilikiLinkMaps' => 'Google Maps',
        'beradaDi' => 'Lokasi',
        'memilikiKategoriUtama' => 'Kategori',
        'dikelolaOleh' => 'Pengelola',
        'memilikiFasilitas' => 'Fasilitas',
        'memilikiJamOperasional' => 'Jam Operasional',
        'memilikiDeskripsi' => 'Deskripsi',
    ];

    if (isset($labels[$name])) {
        return $labels[$name];
    }

    $name = preg_replace('/([a-z])([A-Z])/', '$1 $2', $name) ?? $name;
    return ucfirst($name);
}

try {
    $data = loadRdf($rdfFile, $rdfNamespace, $propertyNamespace);
} catch (Throwable $exception) {
    http_response_code(500);
    $pageError = htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<!doctype html><html lang=\"id\"><head><meta charset=\"utf-8\"><title>Kesalahan RDF</title>";
    echo '<link rel="stylesheet" href="style.css"></head><body><main class="container">';
    echo '<section class="notice error"><h1>RDF tidak dapat dimuat</h1><p>' . $pageError . '</p></section>';
    echo '</main></body></html>';
    exit;
}

$resources = $data['resources'];
$labels = $data['labels'];
$requestedUri = filter_input(INPUT_GET, 'uri', FILTER_UNSAFE_RAW);
$requestedUri = is_string($requestedUri) ? trim($requestedUri) : '';
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($requestedUri === '' && is_string($requestPath) && $requestPath !== '/' && $requestPath !== '/index.php') {
    $resourcePath = trim(rawurldecode($requestPath), '/');
    $requestedUri = $baseUri . $resourcePath;
}
$selectedUri = $requestedUri !== '' ? $requestedUri : null;
$selectedResource = $selectedUri !== null && isset($resources[$selectedUri]) ? $resources[$selectedUri] : null;
$knownUris = array_fill_keys(array_keys($resources), true);
foreach ($resources as $resourceProperties) {
    foreach ($resourceProperties as $values) {
        foreach ($values as $value) {
            if (filter_var($value, FILTER_VALIDATE_URL) !== false) {
                $knownUris[$value] = true;
            }
        }
    }
}
$isKnownUri = $selectedUri !== null && isset($knownUris[$selectedUri]);

if ($selectedUri !== null && !$isKnownUri) {
    http_response_code(404);
}

$title = $selectedUri === null
    ? 'Pariwisata Kota Yogyakarta'
    : displayLabel($selectedUri, $labels);
$imageUrl = is_array($selectedResource)
    ? ($selectedResource['https://wisatayogyakarta.neoverse.my.id/vocabulary#memilikiGambar'][0] ?? null)
    : null;
$mapsUrl = is_array($selectedResource)
    ? ($selectedResource['https://wisatayogyakarta.neoverse.my.id/vocabulary#memilikiLinkMaps'][0] ?? null)
    : null;
$tourismResources = array_filter(
    $resources,
    static fn(array $properties, string $uri): bool => str_contains($uri, '/wisata/'),
    ARRAY_FILTER_USE_BOTH
);
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | Wisata Yogyakarta</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="stylesheet" href="/style.css">
</head>

<body>
    <header class="site-header">
        <div class="container header-content">
            <a class="brand" href="/">
                <span class="brand-mark">Y</span>
                <span>Wisata Yogyakarta</span>
            </a>
        </div>
    </header>

    <main class="container">
        <?php if ($selectedUri !== null && !$isKnownUri): ?>
            <section class="notice error">
                <h1>URI tidak ditemukan</h1>
                <p>URI yang diminta tidak terdapat dalam file RDF.</p>
                <a class="button" href="/">Kembali ke beranda</a>
            </section>
        <?php elseif ($selectedUri !== null): ?>
            <section class="detail-hero">
                <p class="eyebrow">Pariwisata Kota Yogyakarta</p>
                <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
                <?php if (is_string($imageUrl) && filter_var($imageUrl, FILTER_VALIDATE_URL) !== false): ?>
                    <img class="detail-image" src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Foto <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
                <?php endif; ?>
            </section>

            <section class="resource-card" aria-labelledby="properties-title">
                <h2 id="properties-title">Informasi Tempat</h2>
                <?php if ($selectedResource === null): ?>
                    <p class="muted">Resource ini digunakan sebagai nilai relasi dan belum memiliki deskripsi tersendiri di RDF.</p>
                <?php else: ?>
                    <dl class="properties">
                        <?php foreach ($selectedResource as $predicate => $values): ?>
                            <?php $predicateName = basename(str_replace('#', '/', $predicate)); ?>
                            <?php if ($predicateName === 'memilikiGambar'): continue; endif; ?>
                            <div class="property">
                                <dt><?= htmlspecialchars(propertyLabel($predicate), ENT_QUOTES, 'UTF-8') ?></dt>
                                <dd>
                                    <?php foreach ($values as $value): ?>
                                        <?php $isUri = filter_var($value, FILTER_VALIDATE_URL) !== false; ?>
                                        <?php if ($predicateName === 'memilikiLinkMaps' && filter_var($value, FILTER_VALIDATE_URL) !== false): ?>
                                            <a class="resource-link" href="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                                                Buka lokasi di Google Maps
                                            </a>
                                        <?php elseif ($isUri): ?>
                                            <a class="resource-link" href="<?= htmlspecialchars(localUriLink($value), ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars(displayLabel($value, $labels), ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                        <?php else: ?>
                                            <span><?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                <?php endif; ?>
            </section>
            <a class="back-link" href="/">← Kembali ke daftar wisata</a>
        <?php else: ?>
            <section class="hero">
                <div>
                    <p class="eyebrow">Pariwisata Kota Yogyakarta</p>
                    <h1>Temukan pesona<br><span>Yogyakarta</span></h1>
                    <a class="button" href="#daftar-wisata">Jelajahi destinasi</a>
                </div>
                <div class="hero-art" aria-hidden="true">✦</div>
            </section>

            <section id="daftar-wisata" class="listing">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Pilihan Pariwisata</p>
                        <h2>Destinasi pilihan</h2>
                    </div>
                    <span class="count"><?= count($tourismResources) ?> destinasi</span>
                </div>
                <div class="card-grid">
                    <?php foreach ($tourismResources as $uri => $resource): ?>
                        <?php $name = displayLabel($uri, $labels); ?>
                        <article class="destination-card">
                            <div class="card-top">
                                <span class="card-icon">✦</span>
                                <span class="card-type">Wisata</span>
                            </div>
                            <h3><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars($resource[$propertyNamespace . 'memilikiDeskripsi'][0] ?? 'Destinasi wisata di Kota Yogyakarta.', ENT_QUOTES, 'UTF-8') ?></p>
                            <a class="card-link" href="<?= htmlspecialchars(localUriLink($uri), ENT_QUOTES, 'UTF-8') ?>">Lihat detail <span>→</span></a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <footer class="site-footer">
        <div class="container">
            <span>Pariwisata Kota Yogyakarta</span>
        </div>
    </footer>
</body>

</html>