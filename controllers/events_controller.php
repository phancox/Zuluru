<?php
class EventsController extends AppController {

	var $name = 'Events';
	var $components = array('CanRegister');

	function index() {
		if ($this->is_admin) {
			$close = 'DATE_ADD(CURDATE(), INTERVAL -30 DAY)';
		} else {
			$close = 'CURDATE()';
		}
		$this->set('events', $this->Event->find('all', array(
			'conditions' => array(
				'Event.open < DATE_ADD(CURDATE(), INTERVAL 30 DAY)',
				"Event.close > $close",
			),
			'order' => array('Event.event_type_id', 'Event.open', 'Event.close', 'Event.id'),
			'contain' => array('EventType'),
		)));
	}

	function wizard($step = null) {
		if (!$this->is_logged_in) {
			$this->redirect(array('action' => 'index'));
		}
		$id = $this->Auth->user('id');

		// Find any preregistrations
		$prereg = $this->Event->Preregistration->find('list', array(
			'conditions' => array('person_id' => $id),
			'fields' => array('id', 'event_id'),
		));

		// Find all the events that are potentially available
		// TODO: Eliminate the events that don't match the step, if any
		$events = $this->Event->find('all', array(
			'conditions' => array(
				'OR' => array(
					array(
						'Event.open < DATE_ADD(CURDATE(), INTERVAL 30 DAY)',
						'Event.close > CURDATE()',
					),
					'Event.id' => $prereg,
				),
			),
			'order' => array('Event.event_type_id', 'Event.open', 'Event.close', 'Event.id'),
			'contain' => array('EventType'),
		));

		$types = $this->Event->EventType->find('all', array(
			'order' => array('EventType.id'),
		));

		// Prune out the events that are not possible
		foreach ($events as $key => $event) {
			$test = $this->CanRegister->test ($id, $event);
			if (!$test['allowed']) {
				unset ($events[$key]);
			}
		}

		$this->set(compact('events', 'types', 'step'));
	}

	function view() {
		$id = $this->_arg('event');
		if (!$id) {
			$this->Session->setFlash(sprintf(__('Invalid %s', true), __('event', true)), 'default', array('class' => 'info'));
			$this->redirect(array('action' => 'wizard'));
		}
		$this->Event->contain (array(
			'EventType',
			'Division' => array(
				'DivisionGameslotAvailability' => array(
					'GameSlot' => array(
						'Field' => 'Facility',
					),
				),
				'Day',
				'Event' => array(
					'EventType',
					'conditions' => array('Event.id !=' => $id),
				),
			),
		));
		$event = $this->Event->read(null, $id);
		if ($event === false) {
			$this->Session->setFlash(sprintf(__('Invalid %s', true), __('event', true)), 'default', array('class' => 'info'));
			$this->redirect(array('action' => 'wizard'));
		}

		// Extract some more details, if it's a division registration
		if (!empty($event['Event']['division_id'])) {
			// Find the list of facilities and time slots
			$facilities = $times = array();
			foreach ($event['Division']['DivisionGameslotAvailability'] as $avail) {
				$slot = $avail['GameSlot'];
				$facilities[$slot['Field']['Facility']['id']] = $slot['Field']['Facility']['name'];
				$times[$slot['game_start']] = $slot['game_end'];
			}
			asort ($times);
		}

		if ($this->is_logged_in) {
			$this->set ($this->CanRegister->test ($this->Auth->user('id'), $event));
		}

		$this->set(compact ('id', 'event', 'facilities', 'times'));
	}

	function add() {
		if (!empty($this->data)) {
			// Validation requires this information
			$this->data = array_merge ($this->data, $this->Event->EventType->read(null, $this->data['Event']['event_type_id']));

			$this->Event->create();
			if ($this->Event->save($this->data)) {
				$this->Session->setFlash(sprintf(__('The %s has been saved', true), __('event', true)), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(sprintf(__('The %s could not be saved. Please correct the errors below and try again.', true), __('event', true)), 'default', array('class' => 'warning'));
			}
		} else {
			// Set up defaults
			$this->data = array('Event' => array(
					'EventType' => array(
						'type' => 'generic',
					),
			));
		}
		$this->set('eventTypes', $this->Event->EventType->find('list'));
		$this->set('questionnaires', $this->Event->Questionnaire->find('list'));
		$this->set('event_obj', $this->_getComponent ('EventType', $this->data['Event']['EventType']['type'], $this));
		$this->set('add', true);

		if (Configure::read('feature.tiny_mce')) {
			$this->helpers[] = 'TinyMce.TinyMce';
		}

		$this->render ('edit');
	}

	function edit() {
		$id = $this->_arg('event');
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(sprintf(__('Invalid %s', true), __('event', true)), 'default', array('class' => 'info'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			// Validation requires this information
			$type = $this->Event->EventType->read(null, $this->data['Event']['event_type_id']);
			if (empty ($type)) {
				// We need something here to avoid errors
				$type = array('EventType' => array('type' => null));
			}
			$this->data = array_merge ($this->data, $type);

			if ($this->Event->save($this->data)) {
				$this->Session->setFlash(sprintf(__('The %s has been saved', true), __('event', true)), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(sprintf(__('The %s could not be saved. Please correct the errors below and try again.', true), __('event', true)), 'default', array('class' => 'warning'));
			}
		}
		if (empty($this->data)) {
			$this->Event->contain (array (
				'EventType',
			));
			$this->data = $this->Event->read(null, $id);
		}

		$this->set('eventTypes', $this->Event->EventType->find('list'));
		$this->set('questionnaires', $this->Event->Questionnaire->find('list', array('conditions' => array(
				'Questionnaire.active' => true,
		))));
		$this->set('event_obj', $this->_getComponent ('EventType', $this->data['EventType']['type'], $this));

		if (Configure::read('feature.tiny_mce')) {
			$this->helpers[] = 'TinyMce.TinyMce';
		}
	}

	function event_type_fields() {
		Configure::write ('debug', 0);
		$this->layout = 'ajax';
		$this->Event->contain (array (
			'EventType',
		));
		$type = $this->Event->EventType->read(null, $this->params['url']['data']['Event']['event_type_id']);
		$this->set('event_obj', $this->_getComponent ('EventType', $type['EventType']['type'], $this));
	}

	function delete() {
		$id = $this->_arg('event');
		if (!$id) {
			$this->Session->setFlash(sprintf(__('Invalid %s', true), __('event', true)), 'default', array('class' => 'info'));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Event->delete($id)) {
			$this->Session->setFlash(sprintf(__('%s deleted', true), __('Event', true)), 'default', array('class' => 'success'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(sprintf(__('%s was not deleted', true), __('Event', true)), 'default', array('class' => 'warning'));
		$this->redirect(array('action' => 'index'));
	}
}
?>
