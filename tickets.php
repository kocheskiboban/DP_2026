<?php
/** Ticket information: how to buy, prices, discounts, payment, rules. */
require __DIR__ . '/includes/bootstrap.php';

$p = page_text('tickets');
$types = get_ticket_types();
[$upcoming] = get_events(['when' => 'upcoming', 'limit' => 6]);
render_header(['title' => $p['title'], 'description' => $p['meta'], 'body_class' => 'page-tickets']);
echo page_hero($p['eyebrow'], $p['title'], $p['lead'], [[t('nav.tickets'), null]], null,
    '<div class="hero__actions"><a class="btn btn--gold btn--lg" href="' . e(url('programme.php', ['when' => 'upcoming'])) . '">' . icon('ticket') . e(t('nav.buy_tickets')) . '</a>'
    . (is_logged_in() ? '<a class="btn btn--ghost-light btn--lg" href="' . e(url('account/tickets.php')) . '">' . e(t('nav.my_tickets')) . '</a>' : '<a class="btn btn--ghost-light btn--lg" href="' . e(url('account/register.php')) . '">' . e(t('nav.register')) . '</a>') . '</div>');
?>
<section class="section">
  <div class="container">
    <h2><?= e($p['how_h']) ?></h2>
    <ol class="steps" aria-label="<?= e($p['how_h']) ?>">
      <?php foreach ($p['how'] as $step): ?><li class="is-done"><?= e($step) ?></li><?php endforeach; ?>
    </ol>
  </div>
</section>
<section class="section section--paper">
  <div class="container layout-sidebar">
    <div>
      <h2><?= e($p['prices_h']) ?></h2>
      <p><?= e($p['prices_text']) ?></p>
      <div class="table-wrap">
        <table class="price-table">
          <thead><tr><th scope="col"><?= e(t('tickets.category')) ?></th><th scope="col"><?= e(t('tickets.discount')) ?></th><th scope="col"><?= e(t('tickets.conditions')) ?></th></tr></thead>
          <tbody>
          <?php foreach ($types as $tt): ?>
            <tr><th scope="row"><?= e($tt['name']) ?></th><td><?= $tt['discount_percent'] ? '−' . (int) $tt['discount_percent'] . '%' : '—' ?></td><td><?= e($tt['description']) ?></td></tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <h3 style="margin-top:var(--s-6)"><?= e($p['zones_h']) ?></h3>
      <p><?= e($p['zones_text']) ?></p>
      <h2 style="margin-top:var(--s-6)"><?= e($p['payment_h']) ?></h2>
      <ul class="contact-list">
        <?php foreach ($p['payment'] as [$ic, $h, $txt]): ?>
          <li><span class="icon-circle"><?= icon($ic) ?></span><div><strong><?= e($h) ?></strong><?= e($txt) ?></div></li>
        <?php endforeach; ?>
      </ul>
      <h2 style="margin-top:var(--s-6)"><?= e($p['rules_h']) ?></h2>
      <ul><?php foreach ($p['rules'] as $r): ?><li><?= e($r) ?></li><?php endforeach; ?></ul>
    </div>
    <aside class="info-panel">
      <h3><?= icon('clock') ?><?= e($p['boxoffice_h']) ?></h3>
      <p><?= e($p['boxoffice_text']) ?></p>
      <table class="hours"><?php foreach ($p['boxoffice_hours'] as [$d, $h]): ?><tr><td><?= e($d) ?></td><td><strong><?= e($h) ?></strong></td></tr><?php endforeach; ?></table>
      <p style="margin-top:var(--s-4)"><a class="link-arrow" href="<?= e(url('faq.php')) ?>"><?= e(t('nav.faq')) ?><?= icon('arrow-right') ?></a></p>
      <p><a class="link-arrow" href="<?= e(url('verify.php')) ?>"><?= e(t('nav.verify')) ?><?= icon('arrow-right') ?></a></p>
    </aside>
  </div>
</section>
<section class="section" aria-labelledby="t-up">
  <div class="container">
    <?= section_head(t('home.upcoming_eyebrow'), t('tickets.on_sale'), url('programme.php'), t('home.full_programme'), 't-up') ?>
    <div class="event-list"><?php foreach ($upcoming as $ev): ?><?= event_card($ev, 'row') ?><?php endforeach; ?></div>
  </div>
</section>
<?php render_footer();
