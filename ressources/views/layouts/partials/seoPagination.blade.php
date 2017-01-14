<?php
$presenter = new TeenQuotes\Pagination\SeoPresenter($paginator);
?>

<?php if ($paginator->getLastPage() > 1): ?>
  <ul class="pagination">
    <?php echo $presenter->render(); ?>
  </ul>
<?php endif; ?>
