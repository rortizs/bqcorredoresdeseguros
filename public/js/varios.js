function contactoEnviado() {
  var url_parse = window.location.href;
  console.log(url_parse);
  var url = new URL(url_parse);
  var c = url.searchParams.get("contacto");
  if (c == "enviado") {
    $("#myModal").modal("show");
  }
}

function changeState(elemento) {
  if (elemento == "mision") {
    document.getElementById("elemento").innerHTML =
      "Asesorar a nuestros clientes en la gestión de sus seguros y riesgos, cumpliendo el objetivo de brindarle el mejor servicio, y programa de seguros según su necesidad, en conjunto con las aseguradoras.";
    document.getElementById("mision").classList.add("select");
    document.getElementById("vision").classList.add("no-select");
    document.getElementById("conoce").classList.add("no-select");
    document.getElementById("mision").classList.remove("no-select");
    document.getElementById("vision").classList.remove("select");
    document.getElementById("conoce").classList.remove("select");
  }
  if (elemento == "vision") {
    document.getElementById("elemento").innerHTML =
      "Generar un valor agregado a nuestros clientes, basado en ser profesionales, éticos y con capacidad técnica, brindar un servicio al cliente personalizado.";
    document.getElementById("vision").classList.add("select");
    document.getElementById("mision").classList.add("no-select");
    document.getElementById("conoce").classList.add("no-select");
    document.getElementById("vision").classList.remove("no-select");
    document.getElementById("mision").classList.remove("select");
    document.getElementById("conoce").classList.remove("select");
  }
  if (elemento == "conoce") {
    document.getElementById("elemento").innerHTML =
      "Somos un equipo de profesionales con más de 25 años de experiencia en el mercado asegurador y comprometido con la mejora continua, especializándonos en análisis de riesgo y diseño de programas de administración de seguros individuales y para empresas, garantizando la calidad, eficiencia y competitividad";
    document.getElementById("conoce").classList.add("select");
    document.getElementById("vision").classList.add("no-select");
    document.getElementById("mision").classList.add("no-select");
    document.getElementById("conoce").classList.remove("no-select");
    document.getElementById("vision").classList.remove("select");
    document.getElementById("mision").classList.remove("select");
  }
}
