<?php if (!$this->params['isAjax']): ?>

<?php
$this->Html->addCrumb (__('People', true));
$this->Html->addCrumb (__('Inactive Search', true));
?>

<div class="people search">
<h2><?php printf(__('Search %s', true), __('People', true)); ?></h2>

	<?php if (Configure::read('feature.affiliates')): ?>
	<div class="search form">
	<?php echo $this->Form->create(false, array('url' => $url, 'id' => 'RuleSearchForm'));?>
	<?php
	if (isset($affiliate_id)) {
		echo $this->Form->hidden('affiliate_id', array('value' => $affiliate_id));
	} else if (isset($affiliates)) {
		echo $this->ZuluruForm->input('affiliate_id', array(
				'options' => $affiliates,
				'hide_single' => true,
		));
	}
	echo $this->Form->hidden('sort', array('value' => 'last_name'));
	echo $this->Form->hidden('direction', array('value' => 'asc'));

	$spinner = $this->ZuluruHtml->icon('spinner.gif');
	echo $this->Js->submit(__('Search', true), array(
			'url'=> $url,
			'update' => '#SearchResults',
			'evalScripts' => true,
			'beforeSend' => "jQuery('#SearchResults').html('$spinner');",
	));
	echo $this->Form->end();
	?>
	</div>
	<?php endif; ?>

<div id="SearchResults">
<?php endif; ?>

<?php echo $this->element('people/search_results'); ?>

<?php if (!empty($params['rule'])): ?>
<p class="clear">To create a mailing list for this search, use this rule: <code><?php echo $params['rule']; ?></code></p>
<?php endif; ?>

<?php if (!$this->params['isAjax']): ?>
</div>
</div>
<?php endif; ?>
