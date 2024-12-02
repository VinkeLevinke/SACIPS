<!DOCTYPE html>
<html lang="es">

<head>
    <title>Notificaciones</title>
    <link rel="stylesheet" href="../style/tablas.css">  
 <style>
    

.headerDashboard {
    background-color: #ffffff;
    padding: 0.8% 0 1% 0;
    box-shadow: 0 0 6px 0 #373737ba;
    z-index: 1;
    width: 100%;
  
}

.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 4% 0 4%;
    position: relative;

}
  /*----------------- HEADER TEMPLATE ----------------------*/

  .notiContainer{
    display: flex;
    align-items: center;
    border: none;
    position: relative;
 
  }

  .notiContainer .iconHeader{
    width: 1.2em;;
  }

  .notiContainer .iconHeaderAdv{
    width: 0.8em;
    position: absolute;
    right: -5%;
    top: 0;
  }
  

.headerDashboard h3 {
    margin: 0;
    padding: 0;
    text-shadow: 2px 2px 5px #00000013;
    color: rgba(54, 54, 54, 0.95);
    letter-spacing: 2px;
  
    font-size: 1.3rem;
}


.header{
    justify-content: right;

}

 </style>
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body>
    <header class="headerDashboard">
        <div class="header" style="  text-align: right ;">
            <div class="titleDashboard">
                <h3>SACIPS</h3>
            </div>

            <!-- <div class="notis">
                <button id="activarContainerNoti" type="button" class="notiContainer">
                    <img class="iconHeader" src="img/Campana.svg" alt="">
                    <img class="iconHeaderAdv" src="img/advertencia.svg" alt="">
                </button>
                <div class="notiMostrar" id="notiAbierta" style="display:none;"> ocultar inicialmente 
                    <div class="mensajeNuevo">
                        <form action="">
                            <table class="tablaNoti">
                                <tbody></tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div> -->
        </div>
    </header>
    <audio id="notificationSound" src="./assets/noti.wav" preload="auto"></audio>

<!-- Asegúrate de que el script está presente aquí -->
</body>

</html>
