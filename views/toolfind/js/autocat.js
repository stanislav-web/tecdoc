/* 
 * 21.12.2012
 * JS Обработчик для каталога
 * Stanislav WEB
 */


/**
 * Функция логов
 */
function con(e)
{
    return console.info(e);
}

/**
 * Функция всплывающей подсказки tooltip a.title
 */
function toolTip(target_items, name)
{
    $(target_items).each(function(i)
    {
        $("body").append("<div class='"+name+"' id='"+name+i+"'><p>"+$(this).attr('title')+"</p></div>");
        var my_tooltip = $("#"+name+i);

        $(this).removeAttr("title").mouseover(function()
        {
            my_tooltip.css({opacity: "0.8", display:"none"}).fadeIn(400);
        })
        .mousemove(function(kmouse)
        {
            my_tooltip.css({left:kmouse.pageX+15, top:kmouse.pageY+15});
        })
        .mouseout(function()
        {
            my_tooltip.fadeOut(400);
        });
    });
}

//google.load("jquery", "1.6.2"); // подключаем нужную версию jquery

$(document).ready(function() 
{
    //con('JQuery has been loaded!');
    
    if($.browser.msie) 
    {
        //con('msie');
        $('.cols5ie').columnize({ columns: 5 }); // FIX: колонки для IE
    }
    
    /**
     * Инициализирую всплывающую посказку
     */
    toolTip("a.t","tooltip");
    var $alph = $('.alphabet li').find('a'); // ссылка алфавита
      
    /**
     * Древовидное меню
     */
					
    $('#tree span.pm').click(function()
    {
        con('click menu');
        if($(this).hasClass('opened')) $(this).removeClass('opened').next().next().slideUp('fast');
        else $(this).addClass('opened').next().next().slideDown('fast');
    });
    
    /**
     * Обработчик события по клику на ссылку алфавита
     */
    $alph.click(function(){
        var id = $(this).text(); // id вложенного списка в таблице
        var list = $('.alphabet').parent().next().find('li[id]'); // списки с маркерами
        con(id);
        
        /**
         * Если это последний элемент, то показываем весь список
         * Если нет - то фильтруем
         */
        list.hide(); // скрываем все
        //con($(this).parent());
        //con($alph.parent().last());        
        /**
         * Сравниваем объекты jQuery
         * если последний элемент равен кликнувшему, то показываем все
         */
        if($(this).parent().get(0) == $alph.parent().last().get(0)) list.show(); 
        
        list.each(function(){ // перебираю список, чтобы ятобы найти наш id и открыть
            if($(this).attr('id') == id) $(this).show(); // показую
        });
        return false;
    });
});



