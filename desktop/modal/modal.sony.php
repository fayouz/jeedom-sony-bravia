<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}


if (init('id') == '') {
    throw new Exception('{{L\'id de l\'équipement ne peut etre vide : }}' . init('op_id'));
}

$id = init('id');

?>

        <form class="form-horizontal send-code-form">
            <div class="form-group">
                <label class="col-sm-6 control-label">{{Veuillez saisir le code }}</label>

                <div class="col-sm-6">
                    <input type="text" class="sony-code form-control" placeholder="saisir le code"/>
                </div>

            </div>
            <div class="form-group">
            <div class="col-sm-offset-6 col-sm-6 clockdiv">

                <div class="smalltext"> <span class="seconds"></span> Seconds</div>
            </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                <a class="btn btn-success send-code pull-right"><i class="fa fa-check-circle"></i>
                    {{Sauvegarder}}</a>
                <a class="btn btn-danger send-code-close pull-right"><i class="fa fa-minus-circle"></i>
                    {{Annuler}}</a>
                </div>
            </div>
        </form>


        <div class=" send-code-already-done" style="display: none">
            <label class="col-sm-12 control-label">{{L'appareil est deja inscrit}}</label>
            <div class="form-group ">
            <a class="btn btn-danger send-code-close pull-right"><i class="fa fa-minus-circle"></i>
                {{Fermer}}</a>
            </div>
        </div>



<script>

    var deadline = new Date(Date.parse(new Date()) +  59 * 1000);
    initializeClock('clockdiv', deadline);
    authenticateRequestCode(<?php echo $id;?>);


    $('.send-code-close').on('click',function(){
       closeModal();
    });

    $('.send-code').on('click', function(){
        authenticateRequest(<?php echo $id;?>, $('.sony-code').val());
    });

</script>
