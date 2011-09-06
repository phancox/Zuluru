Dear <?php echo $person['first_name']; ?>,

You have been added to the roster of the <?php
echo Configure::read('organization.name'); ?> team <?php echo $team['name']; ?> as a <?php
echo Configure::read("options.roster_position.$position"); ?>.

<?php echo $team['name']; ?> plays in the <?php echo $league['name']; ?> league<?php
if (!empty ($league['Day'])):
?>, which operates on <?php
echo implode (' and ', Set::extract ('/Day/name', $league)); ?><?php
endif;
?>.

More details about <?php echo $team['name']; ?> may be found at
<?php echo Router::url(array('controller' => 'teams', 'action' => 'view', 'team' => $team['id']), true); ?>


If you believe that this has happened in error, please contact <?php echo $reply; ?>.

Thanks,
<?php echo Configure::read('email.admin_name'); ?>

<?php echo Configure::read('organization.short_name'); ?> web team
