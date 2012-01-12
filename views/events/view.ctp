<?php
$this->Html->addCrumb (__('Registration', true));
$this->Html->addCrumb ($event['Event']['name']);
$this->Html->addCrumb (__('View', true));
?>

<div class="events view">
	<h2><?php echo __('View Event', true) . ': ' . $event['Event']['name'];?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $event['Event']['name']; ?>

		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Description'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $event['Event']['description']; ?>
			&nbsp;
		</dd>
<?php if (!empty ($event['Event']['level_of_play'])): ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Level of Play'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $event['Event']['level_of_play']; ?>

		</dd>
<?php endif; ?>

<?php if (!empty($event['Event']['division_id'])): ?>
<?php if (count($facilities) > 0 && count($facilities) < 4):
		$facility_links = array();
		foreach ($facilities as $facility_id => $facility_name) {
			$facility_links[] = $this->Html->link ($facility_name, array('controller' => 'facilities', 'action' => 'view', 'facility' => $facility_id));
		}
?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __(count($facilities) == 1 ? 'Location' : 'Locations'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo implode (', ', $facility_links); ?>

		</dd>
<?php endif; ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('First Game'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->ZuluruTime->Date ($event['Division']['open']); ?>

		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Last Game'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->ZuluruTime->Date ($event['Division']['close']); ?>

		</dd>
		<?php if (!empty ($event['Division']['Day'])): ?>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php __(count ($event['Division']['Day']) == 1 ? 'Day' : 'Days'); ?></dt>
			<dd<?php if ($i++ % 2 == 0) echo $class;?>>
				<?php
				$days = array();
				foreach ($event['Division']['Day'] as $day) {
					$days[] = __($day['name'], true);
				}
				echo implode (', ', $days);
				?>

			</dd>
		<?php endif; ?>
<?php if (count($times) > 0 && count($times) < 5):
		$time_list = array();
		foreach ($times as $start => $end) {
			$time_list[] = $this->ZuluruTime->Time ($start) . '-' . $this->ZuluruTime->Time ($end);
		}
?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __(count($times) == 1 ? 'Game Time' : 'Game Times'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo implode (', ', $time_list); ?>

		</dd>
<?php endif; ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Gender Ratio'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php __(Inflector::Humanize ($event['Division']['ratio'])); ?>

		</dd>
<?php endif; ?>

		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Event Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php __($event['EventType']['name']); ?>

		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Cost'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php
			$cost = $event['Event']['cost'] + $event['Event']['tax1'] + $event['Event']['tax2'];
			if ($cost > 0) {
				echo '$' . $cost;
			} else {
				echo $this->Html->tag ('span', 'FREE', array('class' => 'free'));
			}
			?>

		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Registration Opens'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->ZuluruTime->DateTime ($event['Event']['open']); ?>

		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Registration  Closes'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->ZuluruTime->DateTime ($event['Event']['close']); ?>

		</dd>
<?php if ($event['Event']['cap_female'] == -2): ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Registration Cap'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $event['Event']['cap_male']; ?>

		</dd>
<?php else: ?>
<?php if ($event['Event']['cap_male'] > 0): ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Male Cap'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $event['Event']['cap_male']; ?>

		</dd>
<?php endif; ?>
<?php if ($event['Event']['cap_female'] > 0): ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Female Cap'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $event['Event']['cap_female']; ?>

		</dd>
<?php endif; ?>
<?php endif; ?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Multiples'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php __($event['Event']['multiple'] ? 'Allowed' : 'Not allowed'); ?>

		</dd>
	</dl>

<?php
if (!$is_logged_in):
	echo $this->element('events/not_logged_in');
else:
	foreach ($messages as $message) {
		$class = null;
		if (is_array($message)) {
			$class = $message['class'];
			$message = $message['text'];
		}
		echo $this->Html->para ($class, $message);
	}
	if ($allowed) {
		echo $this->Html->tag ('h2', $this->Html->link(__('Register now!', true),
				array('controller' => 'registrations', 'action' => 'register', 'event' => $id),
				array('title' => __('Register for ', true) . $event['Event']['name'], 'style' => 'text-decoration: underline;')
		));
	}
?>

</div>

<?php if (!empty($event['Division']['Event'])): ?>
<div class="related">
	<h3><?php __('You might alternately be interested in the following registrations:');?></h3>
	<table class="list">
	<tr>
		<th><?php __('Registration'); ?></th>
		<th><?php __('Type');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($event['Division']['Event'] as $related):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $this->Html->link($related['name'], array('controller' => 'events', 'action' => 'view', 'event' => $related['id']));?></td>
			<td><?php __($related['EventType']['name']);?></td>
		</tr>
	<?php endforeach; ?>
	</table>
</div>
<?php endif; ?>

<div class="actions">
	<ul>
<?php if (!empty($event['Event']['division_id'])): ?>
		<li><?php echo $this->Html->link(sprintf(__('View %s', true), __('Division', true)), array('controller' => 'divisions', 'action' => 'view', 'division' => $event['Division']['id'])); ?> </li>
<?php endif; ?>
<?php if ($is_admin): ?>
		<li><?php echo $this->Html->link(sprintf(__('Edit %s', true), __('Event', true)), array('action' => 'edit', 'event' => $event['Event']['id'])); ?> </li>
		<li><?php echo $this->Html->link(sprintf(__('Delete %s', true), __('Event', true)), array('action' => 'delete', 'event' => $event['Event']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $event['Event']['id'])); ?> </li>
		<li><?php echo $this->Html->link(sprintf(__('Edit %s', true), __('Questionnaire', true)), array('controller' => 'questionnaires', 'action' => 'edit', 'questionnaire' => $event['Event']['questionnaire_id'])); ?> </li>
		<li><?php echo $this->Html->link(sprintf(__('List %s', true), __('Preregistrations', true)), array('controller' => 'preregistrations', 'action' => 'index', 'event' => $event['Event']['id'])); ?> </li>
		<li><?php echo $this->Html->link(sprintf(__('New %s', true), __('Preregistration', true)), array('controller' => 'preregistrations', 'action' => 'add', 'event' => $event['Event']['id'])); ?> </li>
		<li><?php echo $this->Html->link(sprintf(__('%s Summary', true), __('Registration', true)), array('controller' => 'registrations', 'action' => 'summary', 'event' => $event['Event']['id'])); ?> </li>
		<li><?php echo $this->Html->link(sprintf(__('Detailed %s List', true), __('Registration', true)), array('controller' => 'registrations', 'action' => 'full_list', 'event' => $event['Event']['id'])); ?> </li>
		<li><?php echo $this->Html->link(sprintf(__('Download %s List', true), __('Registration', true)), array('controller' => 'registrations', 'action' => 'full_list', 'event' => $event['Event']['id'], 'ext' => 'csv')); ?> </li>
<?php endif; ?>
	</ul>
</div>

<?php if (!empty($event['Preregistration']) && $is_admin):?>
<div class="related">
	<h3><?php printf(__('Related %s', true), __('Preregistrations', true));?></h3>
	<table class="list">
	<tr>
		<th><?php __('Person Id'); ?></th>
		<th><?php __('Event Id'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($event['Preregistration'] as $preregistration):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $preregistration['person_id'];?></td>
			<td><?php echo $preregistration['event_id'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'preregistrations', 'action' => 'delete', 'prereg' => $preregistration['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $preregistration['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
</div>
<?php endif; ?>

<?php endif; ?>
