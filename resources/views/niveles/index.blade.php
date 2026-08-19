@extends('layouts.app')

@section('title', 'Niveles | QUANTIKA POOL')

@section('page-title', 'Niveles')

@section('content')

<div class="page-heading">

    <div>

        <h2>
            Niveles de natación
        </h2>

        <p>
            Cada nivel representa una etapa del progreso del alumno.
        </p>

    </div>

</div>


<div class="level-grid">


    <x-level-card
        number="1"
        animal="⭐"
        name="Estrella"
        category="Principiante"
        description="Confianza, adaptación al agua, respiración y ejercicios básicos."
    />


    <x-level-card
        number="2"
        animal="🐚"
        name="Caballito de mar"
        category="Principiante"
        description="Flotación y desplazamientos con apoyo de material."
    />


    <x-level-card
        number="3"
        animal="🪼"
        name="Medusa"
        category="Principiante"
        description="Desplazamientos seguros y flotación sin ayuda de material."
    />


    <x-level-card
        number="4"
        animal="🐙"
        name="Pulpo"
        category="Intermedio"
        description="Movimientos básicos e intermedios de crol y clavados."
    />


    <x-level-card
        number="5"
        animal="🐟"
        name="Pez"
        category="Intermedio"
        description="Dominio de crol y desarrollo del estilo de dorso."
    />


    <x-level-card
        number="6"
        animal="🌊"
        name="Mantarraya"
        category="Intermedio"
        description="Dominio del estilo de dorso y eficiencia en el agua."
    />


    <x-level-card
        number="7"
        animal="🐢"
        name="Tortuga"
        category="Intermedio"
        description="Habilidades y técnicas para dominar el estilo de pecho."
    />


    <x-level-card
        number="8"
        animal="🦭"
        name="Foca"
        category="Avanzado"
        description="Dominio de crol, dorso y pecho, velocidad y resistencia."
    />


    <x-level-card
        number="9"
        animal="🐬"
        name="Delfín"
        category="Avanzado"
        description="Desarrollo de movimientos del estilo de mariposa."
    />


    <x-level-card
        number="10"
        animal="🐋"
        name="Orca"
        category="Avanzado"
        description="Dominio de los cuatro estilos y fundamentos competitivos."
    />


    <x-level-card
        number="11"
        animal="🐳"
        name="Ballena"
        category="Avanzado"
        description="Pruebas de diferentes distancias y estilos de nado."
    />


    <x-level-card
        number="12"
        animal="🦈"
        name="Tiburón"
        category="Avanzado"
        description="Dominio total dentro del agua y alto nivel de destreza."
    />

</div>

@endsection