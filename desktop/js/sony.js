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


$("#table_cmd").sortable({
    axis: "y",
    cursor: "move",
    items: ".cmd",
    placeholder: "ui-state-highlight",
    tolerance: "intersect",
    forcePlaceholderSize: true
});


/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.template
 */
function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
    tr += '</td>';
    tr += '<td>';
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
    tr += '</td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}

/**
 *
 * @param endtime
 * @returns {{total: number, days: number, hours: number, minutes: number, seconds: number}}
 */
function getTimeRemaining(endtime) {
    var t = Date.parse(endtime) - Date.parse(new Date());
    var seconds = Math.floor((t / 1000) % 60);
    var minutes = Math.floor((t / 1000 / 60) % 60);
    var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
    var days = Math.floor(t / (1000 * 60 * 60 * 24));
    return {
        'total': t,
        'days': days,
        'hours': hours,
        'minutes': minutes,
        'seconds': seconds
    };
}

/**
 *
 * @param id
 * @param endtime
 */
function initializeClock(id, endtime) {
    var clock = $('.' + id, '#md_modal');

    var secondsSpan = $('.seconds', clock);

    function updateClock() {

        var t = getTimeRemaining(endtime);
        secondsSpan.text(('0' + t.seconds).slice(-2));

        if (t.total <= 0) {
            closeModal();
            clearInterval(timeinterval);
        }
    }

    updateClock();
    var timeinterval = setInterval(updateClock, 1000);
}


$('.btn-warning').on('click', function () {

    var ip = $('.eqLogicAttr[data-l2key=SONY_IP]').value();
    var regex = new RegExp(/^(?:(?:\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.){3}(?:\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])$/g);
    var result = regex.test(ip);
    if (false == result) {
        bootbox.alert('{{Veuillez définir l ip de la télévision}}');
        return false;
    }

openModal();
});




/**
 *
 */
function closeModal() {
    $('#md_modal').dialog('close');
}


function openModal(){
    $('#md_modal').dialog({
        title: "{{Appairage}}",
        height: 200,
        width: 500,
        position: {my: "center center", at: "center center", of: '#div_pageContainer'}
    });
    $('#md_modal').load('index.php?v=d&modal=modal.sony&plugin=sony&type=sony&id=' + $('.eqLogicAttr[data-l1key=id]').value()).dialog('open');
}

/**
 *
 * @param id
 */
function authenticateRequestCode(id) {




    $.ajax({
        type: 'POST',
        async: false,
        url: 'plugins/sony/core/ajax/sony.ajax.php',
        data: {
            action: 'authenticateRequestCode',
            id: id
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {

        },
        success: function (data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                $('#md_modal').dialog('option', 'height', 250);
                return;
            }

            $('.send-code-form').css('display', 'none');
            $('.send-code-already-done').css('display', 'block');
            $('#md_modal').dialog('option', 'height', 150);
        }
    });
}

/**
 *
 * @param id
 * @param code
 */
function authenticateRequest(id, code) {
    $.ajax({
        type: 'POST',
        async: false,
        url: 'plugins/sony/core/ajax/sony.ajax.php',
        data: {
            action: 'authenticateRequest',
            id: id,
            code: code
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {
        },
        success: function (data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }

            $('#md_modal').dialog("close");
            $('.li_eqLogic[data-eqLogic_id=' + $('.eqLogicAttr[data-l1key=id]').value() + ']').click();
        }
    });
}