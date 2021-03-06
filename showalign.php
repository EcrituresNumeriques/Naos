<?php
$configPassword = "dNh9h{E(\Qm5tB6>";
include('include/config.ini.php');
$getJSON = $bdd->prepare("SELECT * FROM `alignee` where id_alignee = :alignee");
$getJSON->bindParam(":alignee",$_GET[alignee]);
$getJSON->execute() or die('erreur get JSON');
$JSON = $getJSON->fetch();

?>
<!DOCTYPE html>
<html>
    <head>
        <script src="side/jquery-2.1.4.min.js"></script>
        <meta charset="utf-8">
        <title>traduction alignée</title>
    </head>
    <script>
        //p is the ponctuation, t is the word, h is the highlights
        json = '<?=addslashes($JSON['json'])?>';
    </script>
    <style>
        body {
            background: #FFF;
            color: #111;
            font: 14px Baskerville, "Palatino Linotype", "Times New Roman", Times, serif;
            text-align: center;
        }

        div, h1, h2, p {
            margin: 0;
            padding: 0;
        }
        .poem {
            margin:0 5em;
            padding: 0;
            text-align: left;
            width: 500px;
        }
        .prose {
            margin:0 auto;
            padding: 0;
            text-align: justify;
            width: 500px;

        }
        h1, h2 {
            font-weight: normal;
            text-align: center;
        }
        h1 {
            font-size: 34px;
            line-height: 1.2;
            margin-bottom: 10px;
        }
        h2 {
            color: #666;
            font-size: 18px;
            font-style: italic;
            margin-bottom: 30px;
        }
        p {
            line-height: 1.5;
            margin-bottom: 15px;
        }
        h2:before {
            content: '— ';
        }

        h2:after {
            content: ' —';
        }
        .poem h2 + p:first-letter,.prose h2 + p:first-letter {
            font-size: 30px;
            line-height: 1;
            font-weight: bold;
            font-variant: small-caps;
        }
        .poem p:first-line {
        /*    font-variant: small-caps; */
        /*    letter-spacing: 1px; */
        }
        .poem p:last-child {
            margin-bottom: 30px;
        }
        #translation {
        display:flex;
        display:-webkit-flex;
            -webkit-justify-content: center;
            justify-content: center;
        }

        .highlight:hover{
            cursor:pointer;
        }
        .highlight.highlighted{
            background-color:#F4FA58;
        }
    </style>
    <body>
    <div id="translation"></div>
    </body>
    <script>
        $(document).ready(function(){
            console.log('Page loaded');
            data = JSON.parse(json);
            var t=0;
            while(t<data.length){
                var wordid = 0;
                var highlight = "";

                if(data[t].type == "poem"){
                $("#translation").append("<div id=\"text"+t+"\" class=\"poem\"></div>");}
                else if(data[t].type == "prose"){
                $("#translation").append("<div id=\"text"+t+"\" class=\"prose\"></div>");}
                else{
                    $("#translation").append("<div id=\"text"+t+"\"></div>");}
                $("#text"+t).append("<h1 id=\"titre"+t+"\">"+data[t].titre+"</h1>");
                $("#text"+t).append("<h2 id=\"auteur"+t+"\">"+data[t].auteur+"</h2>");



                var s = 0;
                while(s<data[t].text.length){
                    $("#text"+t).append("<p class=\"strophe"+s+"\"></p>");
                    var v = 0;
                    while(v<data[t].text[s].length){
                        var w = 0;
                        while(w<data[t].text[s][v].length){
                            wordid++;
                            if(typeof(data[t].text[s][v][w].t) !== "undefined" && data[t].text[s][v][w].t != "")  {
                                console.log("word"+t);
                                highlight = "";
                                for(o=0;o<data[t].text[s][v][w].h.length;o++){
                                    for(m=0;m<data[t].text[s][v][w].h[o].length;m++){
                                        highlight = highlight+";"+o+"-"+data[t].text[s][v][w].h[o][m];
                                    }
                                }

                                $("div#text"+t+" p.strophe"+s).append("<span id=\""+t+"-"+wordid+"\" class=\"highlight\" data-highlight=\""+highlight+"\" >"+data[t].text[s][v][w].t+"</span> ");
                            }
                            else if(data[t].text[s][v].t != ""){
                                console.log("ponctuation"+t);
                                if(data[t].text[s][v][w].p == "'" || data[t].text[s][v][w].p == "-"){
                                    $("div#text"+t+" p.strophe"+s).html($("div#text"+t+" p.strophe"+s).html().substring(0,$("div#text"+t+" p.strophe"+s).html().length - 1));
                                    $("div#text"+t+" p.strophe"+s).append(data[t].text[s][v][w].p);
                                }
                                else if(data[t].text[s][v][w].p == ":" || data[t].text[s][v][w].p == ";" || data[t].text[s][v][w].p == "," || data[t].text[s][v][w].p == "!" || data[t].text[s][v][w].p == "?" || data[t].text[s][v][w].p == "."){
                                    $("div#text"+t+" p.strophe"+s).html($("div#text"+t+" p.strophe"+s).html().substring(0,$("div#text"+t+" p.strophe"+s).html().length - 1));
                                    $("div#text"+t+" p.strophe"+s).append(data[t].text[s][v][w].p+" ");
                                }
                                else{
                                $("div#text"+t+" p.strophe"+s).append(data[t].text[s][v][w].p+" ");
                                }


                            }
                            w++;
                        }
                        $("div#text"+t+" p.strophe"+s).append("<br>");
                        console.log("line"+t);
                        v++;
                    }
                    s++;
                }
                t++;
            }

            var stropheHeight = 0;
            var nStrophe = 0;
            while(nStrophe<s){
                var stropheHeight = 0;
                $(".strophe"+nStrophe).each(function(){
                    if($(this).innerHeight() > stropheHeight){
                        stropheHeight = $(this).innerHeight();
                    }
                    $(".strophe"+nStrophe).css("height",stropheHeight);
                });
                nStrophe++;
            }

            console.log(s);


            $(".highlight").on("mouseenter",function(){
                var toHighlight = $(this).data("highlight");
                var hl = toHighlight.split(";");
                var i = 1;
                    $(".highlighted").removeClass("highlighted");
                while(i<hl.length){
                    $("#"+hl[i]).addClass("highlighted");
                i++;
                }

            });
            $(".highlight").on("mouseleave",function(){
                    $(".highlighted").removeClass("highlighted");
            });
            $(".highlight").on("click",function(){
                var toHighlight = $(this).data("highlight");
                var hl = toHighlight.split(";");
                var i = 1;
                while(i<hl.length){
                    $("#"+hl[i]).toggleClass("Hardhighlighted");
                i++;
                }

            });


        });
    </script>
</html>
