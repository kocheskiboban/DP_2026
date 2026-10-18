<?php
/** Venue detail with map, seating plan preview and upcoming events. */
require __DIR__ . '/includes/bootstrap.php';

$v = get_venue_by_slug(input('slug', '', 'get'));
if (!$v) {
    not_found();
}
[$upcoming] = get_events(['venue_id' => (int) $v['id'], 'when' => 'upcoming', 'limit' => 12]);
$albums = get_albums(['venue_id' => (int) $v['id']]);
$seats = $v['seating_type'] === 'seated'
    ? db_all('SELECT section, row_label, seat_number, seat_type, is_active, pos_x, pos_y FROM venue_seats WHERE venue_id = ?', [(int) $v['id']])
    : [];
$lat = (float) $v['latitude'];
$lng = (float) $v['longitude'];
$bbox = sprintf('%F,%F,%F,%F', $lng - 0.004, $lat - 0.0025, $lng + 0.004, $lat + 0.0025);
$counts = ['standard' => 0, 'vip' => 0, 'accessible' => 0];
foreach ($seats as $s) {
    if ($s['is_active']) {
        $counts[$s['seat_type']]++;
    }
}

render_header([
    'title' => $v['name'],
    'description' => $v['short_description'],
    'image' => $v['image'],
    'jsonld' => ['@context' => 'https://schema.org', '@type' => 'PerformingArtsTheater', 'name' => $v['name'], 'address' => venue_address($v),
        'geo' => ['@type' => 'GeoCoordinates', 'latitude' => $lat, 'longitude' => $lng], 'maximumAttendeeCapacity' => (int) $v['capacity']],
]);
echo page_hero(t('venues.eyebrow'), $v['name'], $v['short_description'], [[t('nav.venues'), url('venues.php')], [$v['name'], null]], $v['image']);
?>
<section class="section">
  <div class="container layout-sidebar">
    <div>
      <?php if (content_is_fallback('venue_translations', 'venue_id', (int) $v['id'])): ?><p class="notice-fallback"><?= icon('info') ?><?= e(t('content.fallback')) ?></p><?php endif; ?>
      <div class="prose"><h2><?= e(t('venue.about')) ?></h2><?= rich_text($v['description']) ?></div>

      <h2 style="margin-top:var(--s-6)"><?= e(t('venue.seating_plan')) ?></h2>
      <p><?= e($v['seating_info']) ?></p>
      <?php if ($seats): ?>
        <figure class="seatmap-wrap" style="margin:0">
          <?= venue_plan_svg($seats, $v['layout_type'], $v['name']) ?>
          <figcaption>
            <ul class="seat-legend">
              <li><i class="lg-available"></i><?= e(t('seat.standard')) ?> (<?= $counts['standard'] ?>)</li>
              <li><i class="lg-vip"></i><?= e(t('seat.vip')) ?> (<?= $counts['vip'] ?>)</li>
              <li><i class="lg-accessible"></i><?= e(t('seat.accessible')) ?> (<?= $counts['accessible'] ?>)</li>
              <li><i class="lg-unavailable"></i><?= e(t('seat.unavailable')) ?></li>
            </ul>
          </figcaption>
        </figure>
      <?php else: ?>
        <?= alert('info', e(t('venue.general_admission', ['n' => $v['capacity']]))) ?>
      <?php endif; ?>

      <div class="info-panels" style="margin-top:var(--s-6)">
        <div class="info-panel"><h3><?= icon('accessible') ?><?= e(t('venue.accessibility')) ?></h3><p><?= e($v['accessibility']) ?></p></div>
        <div class="info-panel"><h3><?= icon('users') ?><?= e(t('venue.capacity')) ?></h3>
          <p><?= e($v['seating_type'] === 'general' ? t('venue.capacity_general', ['n' => $v['capacity']]) : t('venue.capacity_seated', ['n' => $v['capacity']])) ?></p></div>
      </div>
    </div>
    <aside>
      <div class="info-panel">
        <h3><?= icon('pin') ?><?= e(t('venue.location')) ?></h3>
        <address><?= e(venue_address($v)) ?></address>
        <p class="muted"><?= e(sprintf('%.5f, %.5f', $lat, $lng)) ?></p>
        <iframe class="mini-map" title="<?= e(t('venue.map_title', ['name' => $v['name']])) ?>" loading="lazy"
          src="https://www.openstreetmap.org/export/embed.html?bbox=<?= e($bbox) ?>&amp;layer=mapnik&amp;marker=<?= e($lat . ',' . $lng) ?>"></iframe>
        <div class="links-row" style="margin-top:.8rem">
          <a class="btn btn--ghost btn--sm" href="https://www.openstreetmap.org/?mlat=<?= e((string) $lat) ?>&amp;mlon=<?= e((string) $lng) ?>#map=18/<?= e((string) $lat) ?>/<?= e((string) $lng) ?>" target="_blank" rel="noopener noreferrer"><?= icon('external') ?><?= e(t('venue.open_osm')) ?></a>
          <a class="btn btn--ghost btn--sm" href="<?= e(url('map.php', ['venue' => $v['slug']])) ?>"><?= icon('map') ?><?= e(t('nav.map')) ?></a>
        </div>
      </div>
      <?php if ($albums): ?>
      <div class="info-panel" style="margin-top:var(--s-4)">
        <h3><?= icon('image') ?><?= e(t('nav.gallery')) ?></h3>
        <?php foreach ($albums as $al): ?><p><a href="<?= e(url('album.php', ['slug' => $al['slug']])) ?>"><?= e($al['title']) ?></a> <span class="muted">(<?= (int) $al['image_count'] ?>)</span></p><?php endforeach; ?>
      </div>
      <?php endif; ?>
    </aside>
  </div>
</section>

<section class="section section--paper" aria-labelledby="venue-events">
  <div class="container">
    <?= section_head(t('venue.upcoming_eyebrow'), t('venue.upcoming_title', ['name' => $v['name']]), url('programme.php', ['venue' => $v['slug']]), t('common.view_all'), 'venue-events') ?>
    <?php if ($upcoming): ?>
      <div class="event-list"><?php foreach ($upcoming as $ev): ?><?= event_card($ev, 'row') ?><?php endforeach; ?></div>
    <?php else: ?>
      <?= empty_state('calendar', t('venue.no_upcoming'), '', url('programme.php'), t('nav.programme')) ?>
    <?php endif; ?>
  </div>
</section>
<?php render_footer();
