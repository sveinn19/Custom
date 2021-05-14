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

 class CreateContentForm extends FormBase{

    public function getFormId(){
        return 'create_content_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state){
        $result = $_SESSION['s1'][$_SESSION['s2'] . 's']['items'][$_SESSION['con']];


        $form['text'] = array(
            '#type' => 'textfield',
            '#title' => $result['followers']['total'],
            '#suffix' => "<pre>".print_r($result, true)."</pre>",
        );

        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Submit'),

        );

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        \Drupal::messenger()->addMessage(t('CreateContent'));
        
    }

 }