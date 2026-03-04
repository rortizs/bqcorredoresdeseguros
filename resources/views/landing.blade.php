<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>QBO - Corredores de seguros en Guatemal</title>
    <meta name="description" content="Somos un equipo de profesionales con más de 25 años de experiencia en el mercado asegurador y comprometido con la mejora continua. Ofrecemos los mejores seguros en Guatemala">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="QBO - Asegurador">
    <meta property="og:description" content="Somos un equipo de profesionales con más de 25 años de experiencia en el mercado asegurador y comprometido con la mejora continua. Ofrecemos los mejores seguros en Guatemala.">
    <meta property="og:image" content="https://imageupload.io/E7Z9cRXzTX.i">
    <meta property="og:url" content="http://euro-travel-example.com/index.htm">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XBHY47M8BH"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-XBHY47M8BH');
    </script>

</head>

<body>
    <script src="{{ asset('js/varios.js') }}"></script>
    <div class="header" id="myHeader">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-3">

                    <img class="logo" src="{{ asset('img/QBO-LOGO.png') }}" alt="qbo-aseguradora">

                </div>
                <div class="col-6">
                    <div class="row align-items-center alto">
                        <div class="col-md-2 center">
                            <a href="#first">
                                <p class="bold-f"><b>Inicio</b></p>
                            </a>

                        </div>
                        <div class="col-md-3 center">
                            <a href="#second">
                                <p class="bold-f"><b>Quiénes somos</b></p>
                            </a>
                        </div>
                        <div class="col-md-3 center">
                            <a href="#four">
                                <p class="bold-f"><b>Tipos de seguro</b></p>
                            </a>
                        </div>
                        <div class="col-md-3 center">
                            <a href="#five" class="btn btn-menu bold-f">Quiero cotizar</a>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="row align-items-center alto">
                        <div class="col-3"></div>
                        <div class="col-5 fb-r">
                            <a target="_blank" href="https://www.facebook.com/QBO-Corredores-de-Seguros-109258147660944"><img class="social" src="{{ asset('img/facebook_QBO.png') }}" alt="facebook"></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div id="first">
        <div class="container alto">
            <div class="row alto">
                <div class="col-md-6 alto">
                    <div class="middle-div">
                        <h2 class="bold-f">¿Distintas necesidades? <br> Diferentes opciones</h2>

                        <h1 class="mini-space">Asesoría en seguros personales <br> y empresariales</h1>
                        <div class="space"></div>
                        <a class="btn btn-danger btn-rojo bold-f" href="#five">Quiero saber más</a>
                    </div>
                </div>
            </div>
        </div>
        <a class="ca3-scroll-down-link ca3-scroll-down-arrow" data-ca3_iconfont="ETmodules" data-ca3_icon=""></a>
    </div>
    <div id="second">
        <div id="fondo-2" class="fifty">

        </div>
        <div class="fifty">
            <div class="container alto middle-div2">
                <div class="row alto">
                    <div class="col-md-1"></div>
                    <div class="col-md-8 alto ">
                        <img src="{{ asset('img/QBO-LOGO.png') }}" alt="aseguradora" width="35%">
                        <br><br>
                        <h1 class="conoce bold-f x-space">Conoce quiénes somos</h1>

                        <p id="elemento" class="justify somos">Somos un equipo de profesionales con más de 25 años de experiencia en el mercado asegurador y comprometido con la mejora continua, especializándonos en análisis de riesgo y diseño de programas de administración de seguros individuales
                            y para empresas, garantizando la calidad, eficiencia y competitividad
                        </p>

                        <br>
                        <div class="row">
                            <div class="col-4 center">
                                <button id="conoce" class="select change" onclick="changeState('conoce')">
                                    <h2 class="mm bold-f">Conoce</h2>
                                </button>

                            </div>
                            <div class="col-4 center">
                                <button id="mision" class="no-select change" onclick="changeState('mision')">
                                    <h2 class="mm bold-f">Misión</h2>
                                </button>
                            </div>
                            <div class="col-4 center">
                                <button id="vision" class="no-select change" onclick="changeState('vision')">
                                    <h2 class="mm bold-f">Visión</h2>
                                </button>
                            </div>
                        </div>
                        <br><br>
                        <a class="btn btn-danger btn-rojo bold-f" href="#five">Ir ahora</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="third">
        <div class="container space">
            <div class="row row justify-content-center">
                <div class="col-md-10">
                    <h2 class="title blue bold-f">Nuestros valores</h2>
                </div>
            </div>
        </div>

        <div class="container space">
            <div class="row justify-content-center">
                <div class="col-md-2 center">
                    <img class="logos" src="{{ asset('img/QBO ICONOS-03.png') }}" width="75%" alt="seguros">
                    <br><br>
                    <p class="valores bold-f">Respeto</p>
                </div>
                <div class="col-md-2 center">
                    <img class="logos" src="{{ asset('img/QBO ICONOS-04.png') }}" width="75%" alt="seguros">
                    <br><br>
                    <p class="valores bold-f">Responsabilidad</p>
                </div>
                <div class="col-md-2 center">
                    <img class="logos" src="{{ asset('img/QBO ICONOS-05.png') }}" width="75%" alt="aseguradora">
                    <br><br>
                    <p class="valores bold-f">Integridad</p>
                </div>
                <div class="col-md-2 center">
                    <img class="logos" src="{{ asset('img/QBO ICONOS-06.png') }}" width="75%" alt="valores">
                    <br><br>
                    <p class="valores bold-f">Conciencia <br> social</p>
                </div>
                <div class="col-md-2 center">
                    <img class="logos" src="{{ asset('img/QBO ICONOS-07.png') }}" width="75%" alt="valores">
                    <br><br>
                    <p class="valores bold-f">Servicio</p>
                </div>
            </div>
        </div>
    </div>
    <div id="four">
        <div class="container space">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-9">
                    <h2 class="big white bold-f center">Nos ocupamos de <br>lo que te preocupa</h2>
                    <h1 class="white ti center">Conoce los seguros que tenemos para tí:</h1>
                </div>
            </div>
        </div>
        <br><br>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-3 back-white radius">
                    <p class="title-blue bold-f">Individuales</p>

                    <ul class="lista">
                        <li>Vehículos</li>
                        <li>Vivienda</li>
                        <li>Gastos Médicos</li>
                        <li>Vida</li>
                        <li>Accidentes Personales</li>
                    </ul>
                </div>
                <div class="col-md-1 min"></div>
                <div class="col-md-3 back-white radius ">
                    <p class="title-blue bold-f">Empresariales</p>
                    <ul class="lista">
                        <li>Colectivos de Vida y Gastos Médicos</li>
                        <li>Accidentes Personales para empleados</li>
                        <li>Maquinaría</li>
                        <li>Agrícola</li>
                        <li>Aviación</li>
                        <li>Bienes raíces</li>
                        <li>Comercio</li>
                        <li>Construcción</li>
                        <li>Industria</li>
                        <li>Fianzas</li>
                    </ul>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-3 center mini-space">
                    <a class="btn btn-danger btn-rojo bold-f" href="#five">Cotizar</a>
                </div>
            </div>
        </div>
    </div>
    <div id="five">
        <div class="container space">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-8 center">
                    <h2 class="big blue bold-f">¿Deseas más <br>información?</h2>
                    <h1 class="blue pr">Permítenos ahora conocerte a tí:</h1>
                    <br><br>
                    <form id="contacto" class="needs-validation" action="{{ route('contacto.store') }}" method="post" novalidate>
                        @csrf
                        <div class="form-group">
                            <input type="text" class="form-control input-h @error('nombre') is-invalid @enderror" name="nombre" id="nombre" placeholder="Nombres" value="{{ old('nombre') }}">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control input-h @error('apellido') is-invalid @enderror" name="apellido" id="apellido" placeholder="Apellidos" value="{{ old('apellido') }}">
                            @error('apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control input-h @error('correo') is-invalid @enderror" name="correo" id="correo" placeholder="Correo electrónico*" required value="{{ old('correo') }}">
                            @error('correo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">
                                    Por favor ingrese un correo electrónico valido.
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control input-h @error('telefono') is-invalid @enderror" name="telefono" id="telefono" placeholder="Teléfono" value="{{ old('telefono') }}">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <textarea class="form-control @error('comentario') is-invalid @enderror" id="comentario" name="comentario" rows="4" placeholder="Comentarios">{{ old('comentario') }}</textarea>
                            @error('comentario')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <input class="btn sub-btn bold-f" type="submit" name="submit" value="Enviar">
                    </form>
                </div>

            </div>
        </div>
    </div>
    <div id="six">

    </div>
    <div id="seven">
        <div class="container mini-space">
            <div class="row">

                <div class="col-10">
                    <h1 class="blue midium bold-f">Compañías de seguros con las que trabajamos:</h1>
                </div>
            </div>
            <div class="row align-items-center mini-space">
                <div class="col center"><img class="llogo" src="{{ asset('img/LOGOS-05.jpg') }}" alt="aseguradora"></div>
                <div class="col center"><img class="llogo" src="{{ asset('img/LOGOS-09.jpg') }}" alt="gyt"></div>
                <div class="col center"><img class="llogo" src="{{ asset('img/LOGOS-10.jpg') }}" alt="banco"></div>
                <div class="col center"><img class="llogo" src="{{ asset('img/LOGOS-04.jpg') }}" alt="seguros"></div>
                <div class="col center"><img class="llogo" src="{{ asset('img/LOGOS-11.jpg') }}" alt="el roble"></div>
            </div>
            <div class="row align-items-center space-logo">
                <div class="col center"><img class="llogo" src="{{ asset('img/LOGOS-06.jpg') }}" alt="universales"></div>
                <div class="col center"><img class="llogo" src="{{ asset('img/LOGOS-03.jpg') }}" alt="bam"></div>
                <div class="col center"><img class="llogo" src="{{ asset('img/LOGOS-08.jpg') }}" alt="mapfre"></div>
                <div class="col center"><img class="llogo" src="{{ asset('img/LOGOS-07.jpg') }}" alt="seguros"></div>
                <div class="col center"><img class="llogo" src="{{ asset('img/LOGOS-02.jpg') }}" alt="seguro"></div>
            </div>
        </div>
    </div>
    <div id="footer">
        <div class="container alto">
            <div class="row padd-footer">
                <div class="col-md-3 align-self-start ">
                    <a href="https://www.easpayb.com/coreBrokers/indexGT.html#/inicio" target="_blank" class="size2 m1-txt1 flex-c-m how-btn1 trans-04">
    					<img style="width: 135px" src="https://www.xpertys.com.mx/images/eAspaybAccesos/ea.svg">
    				</a>
                </div>
                <div class="col-md-4 col align-self-center ">
                    <p class="no-margin bold dirr bold-f">Dirección</p>

                    <p class="mini-line">17 Ave. 19-70, Zona 10 Edificio Torino I, <br> Noveno Nivel Oficina 906 Guatemala. </p>
                    <p class="mini-line">Km. 73 Colonia Catalán, Guastatoya <br> El Progreso, Guatemala.</p>

                    <a class="no-a" href="tel:+50222129752">
                        <p class="bold dirr bold-f"> Teléfono: PBX (502) 2212-9752</p>
                    </a>

                </div>
                <div class="col-md-4 align-self-end right ">
                    <p class="dirr"><b>Digicom</b></p>
                </div>
            </div>
        </div>
    </div>
    <div id="myModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Datos enviados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
                </div>
                <div class="modal-body">
                    <p>Sus datos han sido enviados. Pronto será contactado por el equipo de QBO</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/header.js') }}"></script>

    @if(session('success'))
    <script>
        $(document).ready(function() {
            $('#myModal').modal('show');
        });
    </script>
    @endif

</body>

</html>
