<?php
$this->Html->addCrumb (__('Regions', true));
$this->Html->addCrumb (__('List', true));
?>

<div class="regions index">
<h2><?php __('Regions');?></h2>
<table class="list">
	<tr>
		<th><?php __('Name'); ?></th>
		<th class="actions"><?php __('Actions'); ?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($regions as $region):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $region['Region']['name']; ?>&nbsp;</td>
		<td class="actions">
		<?php
		echo $this->ZuluruHtml->iconLink('view_24.png',
			array('action' => 'view', 'region' => $region['Region']['id']),
			array('alt' => __('View', true), 'title' => __('View', true)));
		if ($is_admin) {
			echo $this->ZuluruHtml->iconLink('edit_24.png',
				array('action' => 'edit', 'region' => $region['Region']['id']),
				array('alt' => __('Edit', true), 'title' => __('Edit', true)));
			echo $this->ZuluruHtml->iconLink('delete_24.png',
				array('action' => 'delete', 'region' => $region['Region']['id']),
				array('alt' => __('Delete', true), 'title' => __('Delete', true)),
				array('confirm' => sprintf(__('Are you sure you want to delete # %s?', true), $region['Region']['id'])));
		}
		?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
</div>
<div class="actions">
	<ul>
		<?php
		if ($is_admin) {
			echo $this->Html->tag('li', $this->ZuluruHtml->iconLink('add_32.png',
				array('action' => 'add'),
				array('alt' => __('Add', true), 'title' => __('Add Region', true))));
		}
		?>
	</ul>
</div>
