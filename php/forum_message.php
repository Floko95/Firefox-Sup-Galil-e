<!DOCTYPE html>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="../css/main.css">
	<link rel="stylesheet" type="text/css" href="../css/forum.css">
</head>
<body>
  
<?php require_once ('navigation.html') ?>
  <div class="container top-page">
        <div class="row">
            <div class="col">
                <!--Messages are here !-->
                <!--A message-->
                <div class="row message">
                    <!--Information about the message-->
                    <div  class="col-md-2 user-info ">
                        <div class="row user-name">
                            <div class="col"><p>NAME</p></div>
                        </div>
                        <div class="row message-date">
                            <div class="col-md">Posté le : </div>
                            <div class="col-md">07/02/2019 à 17h30</div>
                        </div>
                    </div>

                    <!--Content of the message-->
                    <div class="col-md-9  message-content">
                        <div class="row message-title">
                            <div class="col title">
                                Titre
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                Bla Bla Bla
                            </div>
                        </div>
                            
                    </div>
                </div>
                <!--A message-->
                <div class="row message">
                    <!--Information about the message-->
                    <div  class="col-md-2 user-info ">
                        <div class="row user-name">
                            <div class="col"><p>NAME</p></div>
                        </div>
                        <div class="row message-date">
                            <div class="col-md">Posté le : </div>
                            <div class="col-md">07/02/2019 à 17h30</div>
                        </div>
                    </div>

                    <!--Content of the message-->
                    <div class="col-md-9  message-content">
                        <div class="row message-title">
                            <div class="col title">
                                Titre
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                Bla Bla Bla
                            </div>
                        </div>
                            
                    </div>
                </div>

                <div class="row new-message">
                    <div class="col">
                        <textarea name="new-message" placeholder="Nouveau message" cols="80" rows="7"></textarea>
                        <button type="subit">Poster</button>
                    </div>
                </div>

                <!--Navigation par page-->
                <div class="row">
                    <div class="offset-md-1 col-md-2">
                        <form action="">
                            <label for="nb_page">Nombre de messages par page</label>
                            <select name="nb_page" size="1">
                                <option>20
                                <option>50
                                <option>100
                            </select><br>
                        </form>
                    </div>
                    <div class="offset-md-2 col-mad-1">
                        prev<br>
                        <button><</button>
                    </div>
                    <div class="offset-md-1 col-mad-1">
                        next<br>
                        <button>></button>
                    </div>
                    
                    
                    </div>
                </div>

            </div>
        </div>

  </div>





  <footer>
    <?php require_once ('footer.html') ?>
  </footer>
</body>

</html>