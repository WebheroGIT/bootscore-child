<?php
// Recupera i post formazione collegati al tirocinio corrente
$formazioni = MB_Relationships_API::get_connected([
    'id'   => 'rel-tir-form', // Sostituisci con l’ID esatto che hai usato nella registrazione della relazione
    'from' => get_the_ID(),
    'type' => 'to',
]);

$valid_cats = [4, 5, 9];
?>

<div class="custom-template-tirocinio">

<?php /* Le relazioni vengono ora mostrate nella sidebar personalizzata (tirocinio_relations) */ ?>



<h2 class="mb-3"><?php the_title(); ?></h2>
<div><?php the_content(); ?></div>

</div>
