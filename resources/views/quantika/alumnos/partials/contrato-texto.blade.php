<div style="text-align:center; font-weight:800; font-size:16px; margin-bottom:4px;">QUANTIKA POOL</div>
<div style="text-align:center; font-size:12px; margin-bottom:14px;">CONTRATO DE ADHESIÓN DE PRESTACIÓN DE SERVICIOS DEPORTIVOS</div>
<div style="text-align:center; font-size:11px; font-weight:700; margin-bottom:18px;">CONSENTIMIENTO INFORMADO • REGLAMENTO INTERNO • CONDICIONES DE INSCRIPCIÓN</div>

<table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:18px;">
    <tr>
        <td style="border:1px solid #ccc; padding:6px 10px; width:220px;">Sede</td>
        <td style="border:1px solid #ccc; padding:6px 10px;">Quantika Pool — {{ $alumno->sucursal->nombre }}</td>
    </tr>
    <tr>
        <td style="border:1px solid #ccc; padding:6px 10px;">Fecha de inscripción</td>
        <td style="border:1px solid #ccc; padding:6px 10px;">{{ optional($alumno->fecha_inscripcion)->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td style="border:1px solid #ccc; padding:6px 10px;">Alumno</td>
        <td style="border:1px solid #ccc; padding:6px 10px;">{{ $alumno->nombreCompleto() }}</td>
    </tr>
    <tr>
        <td style="border:1px solid #ccc; padding:6px 10px;">Contratante / tutor</td>
        <td style="border:1px solid #ccc; padding:6px 10px;">{{ $alumno->nombreTutor() ?? 'No aplica (alumno mayor de edad)' }}</td>
    </tr>
    <tr>
        <td style="border:1px solid #ccc; padding:6px 10px;">Servicio / nivel / horario</td>
        <td style="border:1px solid #ccc; padding:6px 10px;">
            {{ $alumno->nivel?->nombre ?? 'Sin nivel asignado' }}
            @if ($horario)
                — {{ $horario->dia_semana->label() }} {{ mb_substr((string) $horario->hora_inicio, 0, 5) }}
            @endif
        </td>
    </tr>
    <tr>
        <td style="border:1px solid #ccc; padding:6px 10px;">Fecha de inicio y corte</td>
        <td style="border:1px solid #ccc; padding:6px 10px;">{{ optional($alumno->fecha_inscripcion)->format('d/m/Y') }} • Renovación mensual: mismo día</td>
    </tr>
</table>

<h4>I. REQUISITOS DE INSCRIPCIÓN Y CONDICIONES SANITARIAS</h4>
<p>Como condición para la inscripción y permanencia activa del alumno, deberán cumplirse los requisitos administrativos y sanitarios establecidos en este apartado.</p>
<p><strong>Certificado médico obligatorio:</strong> el alumno deberá presentar un certificado médico expedido por un profesional de la salud que indique que es apto para realizar actividad física y natación, que no existe contraindicación médica para utilizar una alberca de uso colectivo y que, al momento de la valoración, no presenta onicomicosis ni otra condición infectocontagiosa que contraindique el uso de la alberca.</p>
<p>Para la primera inscripción, el certificado médico deberá haber sido expedido dentro de los treinta (30) días naturales anteriores a la fecha de inscripción. El certificado deberá renovarse cada doce (12) meses para mantener activa la inscripción.</p>
<p>El establecimiento podrá solicitar una nueva valoración médica antes del vencimiento del certificado cuando exista una lesión, cambio relevante en el estado de salud, condición visible o cualquier circunstancia que razonablemente pueda afectar la seguridad sanitaria del alumno o de los demás usuarios.</p>
<p>El alumno deberá abstenerse de ingresar a la alberca cuando presente onicomicosis, infecciones o lesiones cutáneas, heridas abiertas, conjuntivitis u otras infecciones oculares contagiosas, fiebre, vómito, diarrea u otra condición infectocontagiosa o circunstancia para la cual un médico indique evitar el uso de albercas colectivas.</p>
<p>Además del certificado médico, la inscripción requiere ficha de inscripción debidamente llenada, datos del alumno y contacto de emergencia, aceptación del presente contrato y reglamento, aceptación del Aviso de Privacidad, pago de la cuota de inscripción anual y pago de la primera mensualidad. Para menores de edad se requerirá adicionalmente CURP, identificación del padre, madre o tutor y firma del padre, madre o tutor.</p>

<h4>II. OBJETO DEL CONTRATO</h4>
<p>El presente documento establece los términos y condiciones aplicables a la prestación de servicios deportivos, recreativos y/o de acondicionamiento físico contratados por el usuario con Quantika Pool. Integra las reglas de uso, condiciones de pago, asistencia, reposiciones, seguridad y consentimiento informado.</p>

<h4>III. SERVICIO CONTRATADO E INSCRIPCIÓN</h4>
<p>El usuario contratará el servicio, nivel, horario y periodicidad indicados en su ficha. La asignación de grupo estará sujeta a edad, nivel técnico, disponibilidad, capacidad y criterios de seguridad y pedagógicos. La inscripción es personal e intransferible salvo autorización expresa.</p>

<h4>IV. HORARIOS Y ASISTENCIA</h4>
<p>Las clases iniciarán y terminarán en el horario contratado. No existe tolerancia de tiempo adicional por llegadas tardías; el tiempo perdido no será acumulable, reembolsable ni compensable. La falta de asistencia no constituye por sí misma una solicitud de baja ni genera devolución, descuento o extensión de la mensualidad.</p>

<h4>V. CAMBIOS DE HORARIO, DÍAS DE ASISTENCIA Y GRUPO</h4>
<p>Los cambios podrán realizarse sin costo únicamente en la fecha correspondiente al pago de la mensualidad, sujetos a disponibilidad. En cualquier otra fecha se generará un cargo administrativo de $50.00 M.N.</p>

<h4>VI. REPOSICIONES DE CLASES</h4>
<p>Cada alumno podrá reponer como máximo dos (2) clases por mes, dentro del mismo mes calendario en que ocurrió la falta. En clases de bebés no aplican reposiciones. Las reposiciones no utilizadas se pierden y no generan devolución de dinero, descuentos ni extensión de vigencia.</p>

<h4>VII. INSCRIPCIÓN, CUOTA ANUAL Y MENSUALIDAD</h4>
<p>La cuota de inscripción anual es obligatoria, cubre doce (12) meses y es independiente de las mensualidades. La mensualidad se renueva cada mes en el mismo día calendario en que inició el servicio.</p>

<h4>VIII. PAGO, PERIODO DE GRACIA, RECARGO Y SUSPENSIÓN</h4>
<p>El usuario cuenta con cinco (5) días naturales posteriores a la fecha de corte para pagar sin recargo. A partir del sexto día se generará un recargo del diez por ciento (10%) y se suspenderá el acceso hasta regularizar el adeudo. Si el adeudo permanece cuatro (4) meses consecutivos, procederá la baja administrativa.</p>

<h4>IX. DESCUENTO PERMANENTE PARA FAMILIARES DIRECTOS</h4>
<p>Primera persona: 100% de la mensualidad. Segunda persona: 10% de descuento. Tercera persona y adicionales: 20% de descuento. No acumulable con otras promociones salvo autorización expresa.</p>

<h4>X. CANCELACIONES, BAJAS Y SUSPENSIONES TEMPORALES</h4>
<p>La solicitud de baja deberá realizarse por los medios establecidos; la simple inasistencia no constituye baja.</p>

<h4>XI. SALUD, APTITUD FÍSICA Y DECLARACIÓN</h4>
<p>El usuario declara que la información proporcionada sobre su estado físico es verdadera, completa y actualizada, y se compromete a informar oportunamente cualquier lesión, condición o restricción relevante, y a no ingresar bajo efectos de alcohol, drogas o sustancias que alteren sus capacidades.</p>

<h4>XII. CONSENTIMIENTO INFORMADO Y RIESGOS INHERENTES</h4>
<p>El usuario reconoce que la natación implica riesgos inherentes (resbalones, caídas, golpes, calambres, fatiga, ingestión accidental de agua, entre otros), incluso observando medidas ordinarias de seguridad, y se compromete a cumplir instrucciones y comunicar cualquier situación de riesgo.</p>

<h4>XIII. ACCIDENTES Y EMERGENCIAS</h4>
<p>Ante un accidente o emergencia, el establecimiento activará sus protocolos, proporcionará primeros auxilios dentro de sus posibilidades y contactará al número de emergencia proporcionado.</p>

<h4>XIV. REGLAMENTO DE USO DE LAS INSTALACIONES</h4>
<p>Respetar al personal, instructores y demás usuarios; no correr, empujar ni jugar bruscamente; ingresar a la alberca únicamente con autorización; utilizar traje de baño y equipo adecuado; respetar áreas restringidas e indicaciones de seguridad.</p>

<h4>XV. NORMAS DE USO DE ALBERCA Y CLASES</h4>
<p>Es obligatorio ducharse antes de ingresar y utilizar gorra y goggles cuando correspondan. El instructor no se considera exclusivo; Quantika Pool podrá realizar cambios de instructor, grupo u horario por razones operativas, pedagógicas o de seguridad. En caso de cuatro faltas consecutivas injustificadas, Quantika Pool podrá reasignar el espacio del alumno.</p>

<h4>XVI. CALENDARIO INSTITUCIONAL 2026–2027</h4>
<p>Suspensión de labores: 15 y 16 de septiembre de 2026; 2 y 16 de noviembre de 2026; 1 de febrero de 2027; 15 de marzo de 2027; y 5 de mayo de 2027. Vacaciones: del 21 de diciembre de 2026 al 2 de enero de 2027. Mantenimiento anual: 4 y 5 de enero de 2027. Los días de asueto oficial no generan reposición ni compensación.</p>

<h4>XVII. MENORES DE EDAD</h4>
<p>Los menores deberán permanecer bajo supervisión del padre, madre, tutor o persona autorizada antes, durante y después de su clase. El tutor deberá notificar cualquier cambio relevante en datos de contacto, condiciones de salud o personas autorizadas para recoger al menor.</p>

<h4>XVIII. OBJETOS PERSONALES Y XIX. ESTACIONAMIENTO</h4>
<p>El usuario será responsable de sus objetos personales y de su vehículo; Quantika Pool no presta servicio de custodia de vehículos ni pertenencias.</p>

<h4>XX. USO DE IMAGEN</h4>
<p>La autorización para captar y utilizar fotografías o videos del alumno con fines publicitarios o institucionales deberá constar en una autorización específica. La negativa no impide la contratación del servicio.</p>

<h4>XXI. DATOS PERSONALES Y AVISO DE PRIVACIDAD</h4>
<p>Los datos personales serán tratados conforme al Aviso de Privacidad Integral puesto a disposición del titular.</p>

<h4>XXII. CAMBIOS OPERATIVOS</h4>
<p>El establecimiento podrá realizar ajustes razonables de instructor, grupo, nivel, horarios o dinámica de clase cuando sean necesarios por razones pedagógicas, de seguridad, mantenimiento o funcionamiento.</p>

<h4>XXIII. CONDUCTAS QUE PUEDEN GENERAR SUSPENSIÓN O BAJA</h4>
<p>Agresiones físicas o verbales, conductas de riesgo deliberado, daño intencional a instalaciones, ingreso bajo efectos de alcohol o drogas, falsificación de información, o incumplimiento grave o reiterado del presente contrato.</p>

<h4>XXIV a XXVI. QUEJAS, VIGENCIA Y LEGISLACIÓN APLICABLE</h4>
<p>El presente contrato tendrá vigencia mientras permanezca activa la relación de prestación de servicios. Las partes se sujetarán a las disposiciones legales aplicables en los Estados Unidos Mexicanos.</p>

<h4>XXVII. ACEPTACIÓN</h4>
<p>El usuario manifiesta haber leído y comprendido el presente documento, las condiciones del servicio contratado y el reglamento interno, y declara haber proporcionado información veraz.
@if ($esMenorDeEdad)
    En el caso de menores de edad, el padre, madre o tutor manifiesta que cuenta con facultades para suscribir el presente documento y autoriza la participación del menor en las actividades contratadas.
@endif
</p>
