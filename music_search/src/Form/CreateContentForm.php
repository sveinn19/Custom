<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Drupal\Core\Database;

/**
* Form for searching music.
 */

 class CreateContentForm extends FormBase{

    public function __construct($spotify_service, $discogs_service) {  
        $this->spotify_service = $spotify_service;
        $this->discogs_service = $discogs_service;
        }
    
        /** * {@inheritdoc} 
         * */
    public static function create(ContainerInterface $container) { 
         return new static(    
           $container->get('spotify_lookup.spotify_lookup'),
           $container->get('discogs_lookup.discogs_lookup')
           );
         }

    public function getFormId(){
        return 'create_content_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state){
        $result = $_SESSION['s1'][$_SESSION['s2'] . 's']['items'];//[$_SESSION['con']];
        $_SESSION['result'] = $result;

        $this->get_inputs();

        if ($_SESSION['s2'] == 'artist' || $_SESSION['s2'] == 'album'){

            $form['nafn'] = array(
                '#type' => 'radios',
                '#title' => 'Choose name: ',
                '#options' => $this->getNames($_SESSION['spot-res'], $_SESSION['dc-res']),
                //'#suffix' => "<h2>Spotify</h2><pre>". print_r($_SESSION['spot-res'], true)."</pre>",
                //'#prefix' => "<h2>Discogs</h2><pre>". print_r($_SESSION['dc-res'], true)."</pre>",
                //'#suffix' => "<pre>".print_r($
            );
        }

        if ($_SESSION['s2'] == 'artist' || $_SESSION['s2'] == 'album'){

            $form['image'] = array(
                '#type' => 'radios',
                '#title' => 'Choose image: ',
                '#options' => $this->getImages($_SESSION['spot-res'], $_SESSION['dc-res']),
                //'#suffix' => "
            );
        }

        if ($_SESSION['s2'] == 'artist'){

            $form['site'] = array(
                '#type' => 'radios',
                '#title' => 'Choose website: ',
                '#options' => $this->getSites($_SESSION['spot-res'], $_SESSION['dc-res']),
                //'#suffix' => "
            );

            $form['members'] = array(
                '#type' => 'checkboxes',
                '#title' => 'Add members: ',
                '#options' => $this->getMembers($_SESSION['dc-res']),
                //'#suffix' => "
            );
        }

        if ($_SESSION['s2'] == 'artist' || $_SESSION['s2'] == 'album'){

            $form['description'] = array(
                '#type' => 'radios',
                '#title' => 'Choose description: ',
                '#options' => $this->getDescr($_SESSION['dc-res']),
                //'#suffix' => "
            );
        }

        if ($_SESSION['s2'] == 'album'){

            $form['artist_name'] = array(
                '#type' => 'radios',
                '#title' => 'Choose artist: ',
                '#options' => $this->getArtist($_SESSION['spot-res'], $_SESSION['dc-res']),
                //'#suffix' => "
            );

            $form['genre'] = array(
                '#type' => 'radios',
                '#title' => 'Choose genre: ',
                '#options' => $this->getGenres($_SESSION['dc-res']),
                //'#suffix' => "
            );

            $form['tracks'] = array(
                '#type' => 'checkboxes',
                '#title' => 'Choose songs: ',
                '#options' => $this->getTracks($_SESSION['spot-res']),
                //'#suffix' => "
            );

            $form['label'] = array(
                '#type' => 'radios',
                '#title' => 'Choose label: ',
                '#options' => $this->getLabels($_SESSION['spot-res'], $_SESSION['dc-res']),
                //'#suffix' => "
            );

        }

        if ($_SESSION['s2'] == 'track'){
            $form['track'] = array(
                '#type' => 'radios',
                '#title' => 'Song to create: ',
                //'#options' => $this->getLabels($_SESSION['spot-res'], $_SESSION['dc-res']),
                '#suffix' => '<img src='. '"' . $_SESSION['s1']['tracks']['items'][(int)$_SESSION['con']]['album']['images'][0]['url'] . '" width="100" align="center">'.'<strong>'.$_SESSION['s1']['tracks']['items'][(int)$_SESSION['con']]['name'].' (Spotify) </strong>'.t('  Duration: '). round($_SESSION['s1']['tracks']['items'][(int)$_SESSION['con']]['duration_ms']/60000, 2).'<hr>',
               // print_r($_SESSION['s1']['tracks']['items'][(int)$_SESSION['con']]['external_urls']['spotify'], true),
                //$_SESSION['s1']['tracks']['items'][(int)$_SESSION['con']]['name']
            );

        }



        //$node_title = \Drupal::database()->select('node_field_nafn', 'Travis');

    //     $database = \Drupal::database();
    //     $query = $database->query("SELECT nid FROM {node} WHERE type='listamadur'");
    //     $result_db = $query->fetchAll();
    //    // \Drupal\node\Entity\Node::load(get_object_vars($result_db[0])['nid'])->title->getValue()[0]['value']
    //     $tmp = [];
    //     foreach($result_db as $key => $value){
    //         array_push($tmp, \Drupal\node\Entity\Node::load(get_object_vars($value)['nid'])->title->getValue()[0]['value']);
    //    }


        // $form['text'] = array(
        //     '#type' => 'checkboxes',
        //     '#title' => 'Choose what to create',
        //     '#suffix' => "<h2>Spotify</h2><pre>". print_r($_SESSION['spot-res'], true)."</pre>",
        //     //'#suffix' => "<h2>Spotify</h2><pre>". print_r($_SESSION['spot-res'], true)."</pre>",
        //     '#prefix' => "<h2>Discogs</h2><pre>". print_r($_SESSION['dc-res'], true)."</pre>",
        //     //'#suffix' => "<pre>".print_r($result, true)."</pre>",
        // );

        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Submit'),

        );

        return $form;
    }

    private function getLabels($spotify, $discogs){
        $option = [];
        foreach($spotify as $key => $value){
            $option[$value['label']] = ($value['label']).t('  (Spotify)');
            
        }

        foreach($discogs as $key => $value){
            foreach($value['labels'] as $key2 => $value2){
                $option[$value2['name']] = ($value2['name']).t('  (Discogs)');
            }
        }

        return $option;

    }

    private function getTracks($spotify){
        $option = [];
        foreach($spotify as $key => $value){
            foreach($value['tracks']['items'] as $key2 => $value2){
                $option[$value2['href']] = $value2['name'] . '. (Duration: ' . round((int)$value2['duration_ms']/60000, 2) . ' mín)' . ' (Spotify)';

            }
            
        }

        return $option;

    }

    private function getGenres($discogs){
        $option = [];
        foreach($discogs as $key => $value){
            foreach($value['genres'] as $key2 => $value2){
                $option[$value2] = ($value2).t('  (Discogs)');
            }
        }

        return $option;

    }
    private function getArtist($spotify, $discogs){
        //TODO FIX 

        $option = [];
        foreach($spotify as $key => $value){
            $option[$value['artists'][0]['name']] = t($value['artists'][0]['name']).t('  (Spotify)');
            
        }

        foreach($discogs as $key => $value){
            $option[$value['artists'][0]['name']] = ($value['artists'][0]['name']).t('  (Discogs)');

        }

        return $option;

    }

    private function getDescr($discogs){
        $option = [];
        foreach($discogs as $key => $value){
            if ($_SESSION['s2'] == 'artist'){
                $option[$value['profile']] = $value['profile'] . '  (Discogs)';
            }
            elseif ($_SESSION['s2'] == 'album'){
                $option[$value['notes']] = $value['notes'] . '  (Discogs)';
            }
        }
        return $option;
    }

    private function getNames($spotify, $discogs){
        $option = [];
        foreach($spotify as $key => $value){
            if (!isset($option[$value['name']])){
                $option[$value['name']] = ($value['name']).t('  (Spotify)');
            }
        }

        foreach($discogs as $key => $value){
            if($_SESSION['s2'] == 'artist'){
                if (!isset($option[$value['name']])){
                    $option[$value['name']] = ($value['name']).t('  (Discogs)');
                }
                if (!isset($option[$value['realname']])){
                    $option[$value['realname']] = ($value['realname']).t('  (Discogs)');
                }
            }
            elseif($_SESSION['s2'] == 'album'){
                $option[$value['title']] = ($value['title']).t('  (Discogs)');

            }
        }


        return $option;
    }

    private function getImages($spotify, $discogs){
        $option = [];
        foreach($spotify as $key => $value){
            $option[$value['images'][0]['url']] = '<img src='. '"' . $value['images'][0]['url'] . '" width="100" > (  Spotify)';
            
        }

        foreach($discogs as $key => $value){
            $option[$value['cover_image']] = '<img src='. '"' . $value['cover_image'] . '" width="100"> (  Discogs)';
        }


        return $option;

    }


    private function getSites($spotify, $discogs){
        $option = [];
        foreach($spotify as $key => $value){
            $option[$value['external_urls']['spotify']] = $value['external_urls']['spotify'];
            
        }

        foreach($discogs as $key => $value){
            $c = 0;
            foreach($value['urls'] as $key2 => $value2){
                if($c !== 5){
                    $option[$value2] = $value2;
                }
                $c++;

            }
        }


        return $option;
    }

    private function getMembers($discogs){
        $option = [];
        foreach($discogs as $key => $value){
            if (isset($value['members'])){
                foreach($value['members'] as $key2 => $value2){
                    $option[$value2['name']] = $value2['name'];
                }
            }
            
        }
        return $option;
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        \Drupal::messenger()->addMessage(t('CreateContent  ')); //. print_r($form_state->getValues('tracks'), true));

        // $result = $_SESSION['spot-res'][0];
        // $url = substr($result['external_urls']['spotify'], 8, strlen($result['external_urls']['spotify']));
        if($_SESSION['s2'] == 'artist'){

        $node = Node::create([
            'type'  => 'listamadur',
            'title' =>  $form_state->getValue('nafn'), //$result['name'],
            'body' => $form_state->getValue('description'),
          ]);
          $node->field_nafn->value = $form_state->getValue('nafn');
          if(isset($form['site'])){
            $node->set('field_vefsida_listamanns', [
                'uri' => $form_state->getValue('site'),//$result['external_urls']['spotify'],
                'title' =>$form_state->getValue('site'),// $result['external_urls']['spotify'],
                'options' => [],
            ]);
          }
        
          $node->save();
        }
        elseif($_SESSION['s2'] == 'album'){
            $track_nodes = $this -> createTrackNode($_SESSION['spot-res'], $form_state);
            $artist_node = $this -> createArtistNode($form_state);
            $label_node = $this -> createLabelNode($form_state);
            //$genre_node = $this -> createGenreNode($form_state);
            $node = Node::create([
                'type' => 'plata',
                'title' => $form_state -> getValue('nafn'),
                
            ]);
            
            // foreach($form_state->getValue('tracks') as $key => $value){
            //     $node->field_nafn_a_lagi = array(
            //         'target_id' => $this->createTrackNode($value)->id(),
            //         //'spotifyid' => 'lag',
            //         //'target_type' => 'lag',
            //         'options' => [],
            //       );    
            // }
            foreach($track_nodes as $key => $value){
                $node->field_nafn_a_lagi = array(
                    'target_id' => $value,
                    //'spotifyid' => 'lag',
                    //'target_type' => 'lag',
                    'options' => [],
                );
            }

            $node->field_lysing->value = $form_state->getValue('description');
            $node->field_flytjandi =  array(
                'target_id' => $artist_node -> id(),
                'options' => [],
            );
            //$node ->field_typa = array(
            //    'target_id' => $genre_node -> id(),
            //    'options' => [],
            //);
            if ( isset($form['label'])){
                $node ->field_utgefandi = array(
                    'target_id' => $label_node -> id(),
                    'options' => [],
                );
            }
            //$node ->field_utgafuar -> value = '1987';
            $node->field_spotify -> value = $this->findSpotifyLink($_SESSION['spot-res'], $form_state -> getValue('nafn'));
        } else{

            $node = Node::create([
                'type'  => 'lag',
                'title' =>  $_SESSION['s1']['tracks']['items'][(int)$_SESSION['con']]['name'], //$result['name'],
                //'body' => $form_state->getValue('description'),
              ]);
              $node->field_nafn->value = $_SESSION['s1']['tracks']['items'][(int)$_SESSION['con']]['name'];
              $node->field_lengd->value = round($_SESSION['s1']['tracks']['items'][(int)$_SESSION['con']]['duration_ms']/60000, 2);

                $node->set('field_spotifyid', [
                    'uri' => $_SESSION['s1']['tracks']['items'][(int)$_SESSION['con']]['external_urls']['spotify'],//$result['external_urls']['spotify'],
                    'title' =>$_SESSION['s1']['tracks']['items'][(int)$_SESSION['con']]['external_urls']['spotify'],// $result['external_urls']['spotify'],
                    'options' => [],
                ]);
              
            
              $node->save();
            //'#suffix' => '<img src='. '"' . $_SESSION['s1']['tracks']['items'][(int)$_SESSION['con']]['album']['images'][0]['url'] . '" width="100" align="center">'.'<strong>'.$_SESSION['s1']['tracks']['items'][(int)$_SESSION['con']]['name'].' (Spotify) </strong>'.t('  Duration: '). ($_SESSION['s1']['tracks']['items'][(int)$_SESSION['con']]['duration_ms']).'<hr>',
        }

        $node -> save();

        
        //$this -> createTrackNode();
    }

    private function findSpotifyLink($spotify, $name){
        foreach($spotify as $key => $value){
            if($value['name'] == $name){
                return $value['external_urls']['spotify'];
            }
        }
    }

    private function createTrackNode($spotify, $form_state){

        $tmp = [];
        $store = [];

        foreach($spotify as $key => $value){
            foreach($value['tracks']['items'] as $key2 => $value2){
                foreach($form_state->getValue('tracks') as $key3 => $value3){
                    if($value2['href'] == $value3 && !in_array($value3, $store)){
                        $node = Node::Create([
                            'type' => 'lag',
                            'title' => $value2['name'],           
                        ]);
                        $node ->field_lengd -> value= $value2['duration_ms'];
                        $node->set('field_spotifyid', [
                            'uri' => $value2['external_urls']['spotify'],//$res['external_links']['spotify'],//$result['external_urls']['spotify'],
                            'title' => $value2['external_urls']['spotify'],//$res['external_links']['spotify'],// $result['external_urls']['spotify'],
                            'options' => [],
                          ]);
                        $node->save();
                        array_push($tmp, $node->id());
                        array_push($store, $value3);
                    }
            }


                //$option[$value2['href']] = $value2['name'] . '. (Duration: ' . round((int)$value2['duration_ms']/60000, 2) . ' mín)' . ' (Spotify)';
            }
        }

        return $tmp;

    //     return $option;
    //    // $res = $this->spotify_service->_spotify_api_get_query($uri);
    //     $node = Node::Create([
    //         'type' => 'lag',
    //         'title' => 'lag',           
    //     ]);
    //     $node ->field_lengd -> value= $res['duration_ms'];
    //     $node->set('field_spotifyid', [
    //         'uri' => 'https://www.visir.is/',//$res['external_links']['spotify'],//$result['external_urls']['spotify'],
    //         'title' => 'https://www.visir.is/',//$res['external_links']['spotify'],// $result['external_urls']['spotify'],
    //         'options' => [],
    //       ]);
    //     $node->save();
    //     return $node;
    }

    private function createArtistNode($form_state){
        $database = \Drupal::database();
        $query = $database->query("SELECT nid FROM {node} WHERE type='listamadur'");
        $result_db = $query->fetchAll();
       // \Drupal\node\Entity\Node::load(get_object_vars($result_db[0])['nid'])->title->getValue()[0]['value']
      // $tmp = [];
       foreach($result_db as $key => $value){
            $tmp_node = \Drupal\node\Entity\Node::load(get_object_vars($value)['nid']);
            if($form_state->getValue('artist_name') == $tmp_node->title->getValue()[0]['value']){
                return $tmp_node;
            }
            //array_push($tmp, \Drupal\node\Entity\Node::load(get_object_vars($value)['nid'])->title->getValue()[0]['value']);
       }

        $node = Node::create([
            'type'  => 'listamadur',
            'title' =>  $form_state->getValue('artist_name'), //$result['name'],
            'body' => $form_state->getValue('description'),
          ]);
          $node->field_nafn->value = $form_state->getValue('artist_name');
          $node->set('field_vefsida_listamanns', [
            'uri' => $this->findSpotifyLink($_SESSION['spot-res'], $form_state->getValue('artist_name')),//$result['external_urls']['spotify'],
            'title' => 'Spotify hlekkur',// $result['external_urls']['spotify'],
            'options' => [],
          ]);

        $node ->save();
        return $node;

    }

    private function createLabelNode($form_state){
        $database = \Drupal::database();
        $query = $database->query("SELECT nid FROM {node} WHERE type='utgefandi'");
        $result_db = $query->fetchAll();
       // \Drupal\node\Entity\Node::load(get_object_vars($result_db[0])['nid'])->title->getValue()[0]['value']
      // $tmp = [];
       foreach($result_db as $key => $value){
            $tmp_node = \Drupal\node\Entity\Node::load(get_object_vars($value)['nid']);
            if($form_state->getValue('label') == $tmp_node->title->getValue()[0]['value']){
                return $tmp_node;
            }
            //array_push($tmp, \Drupal\node\Entity\Node::load(get_object_vars($value)['nid'])->title->getValue()[0]['value']);
       }

        $node = Node::create([
            'type'  => 'utgefandi',
            'title' =>  $form_state->getValue('label'), //$result['name'],
            'body' => $form_state->getValue('description'),
          ]);
            
        $node -> save();
        return $node;  
    }

    private function createGenreNode($form_state){
        $node = Node::create([
            'type'  => 'tegund_tonlistar',
            'title' =>  $form_state->getValue('genre'), //$result['name'],
            'body' => $form_state->getValue('genre'),
          ]);
        $node -> save();
        return $node; 
    }

    private function get_inputs(){
        $temp_sp = [];
        $temp_dc = [];

        foreach($_SESSION['con'] as $key => $value){
            if ($value !== 0){
                //$temp[$key] = [substr($value, -2), substr($value, 0, -2)];
                if (substr($value, -2) == 'sp'){
                    if($_SESSION['s2'] == 'artist' || $_SESSION['s2'] == 'track'){
                        array_push($temp_sp, $_SESSION['s1'][$_SESSION['s2'] . 's']['items'][(int)substr($value, 0, -2)]);
                    }
                    elseif($_SESSION['s2'] == 'album'){
                        array_push($temp_sp, $this->spotify_service->_spotify_api_get_query($_SESSION['s1'][$_SESSION['s2'] . 's']['items'][(int)substr($value, 0, -2)]['href']));
                    }
                }
                else {
                    $tmp = $this->discogs_service->_discogs_api_get_query($_SESSION['d1']['results'][(int)substr($value, 0, -2)]['resource_url']);
                    $tmp['cover_image'] = $_SESSION['d1']['results'][(int)substr($value, 0, -2)]['cover_image'];
                    array_push($temp_dc, $tmp);
                }
            }
        }

        $_SESSION['spot-res'] = $temp_sp;
        $_SESSION['dc-res'] = $temp_dc;
    }

 }