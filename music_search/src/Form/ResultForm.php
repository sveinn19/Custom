<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
* Form for searching music.
 */

 class ResultForm extends FormBase{

    public function getFormId(){
        return 'result_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state){

        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Submit'),

        );

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        \Drupal::messenger()->addMessage(t('Jibbi'));
    }

 }