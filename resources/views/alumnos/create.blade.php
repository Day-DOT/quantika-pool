@extends('layouts.app')

@section('title', 'Registrar alumno | QUANTIKA POOL')

@section('page-title', 'Nuevo alumno')

@section('content')

<div class="page-heading">

    <div>

        <h2>
            Registrar alumno
        </h2>

        <p>
            Agrega la información del alumno y su responsable.
        </p>

    </div>

</div>


<form>

    <section class="panel">

        <div class="panel-header">

            <h3>
                Información del alumno
            </h3>

        </div>


        <div class="panel-body">

            <div class="form-grid">


                <div class="form-group">

                    <label>
                        Nombre
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        placeholder="Nombre"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Apellidos
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        placeholder="Apellidos"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Fecha de nacimiento
                    </label>

                    <input
                        type="date"
                        class="form-control"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Sucursal
                    </label>

                    <select class="form-control">

                        <option>QUANTIKA</option>

                        <option>AQUALIX</option>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Nivel actual
                    </label>

                    <select class="form-control">

                        <option>⭐ Estrella</option>

                        <option>🐚 Caballito de mar</option>

                        <option>🪼 Medusa</option>

                        <option>🐙 Pulpo</option>

                        <option>🐟 Pez</option>

                        <option>🌊 Mantarraya</option>

                        <option>🐢 Tortuga</option>

                        <option>🦭 Foca</option>

                        <option>🐬 Delfín</option>

                        <option>🐋 Orca</option>

                        <option>🐳 Ballena</option>

                        <option>🦈 Tiburón</option>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Teléfono
                    </label>

                    <input
                        type="tel"
                        class="form-control"
                        placeholder="55 0000 0000"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Nombre del tutor
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        placeholder="Tutor / responsable"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Correo del tutor
                    </label>

                    <input
                        type="email"
                        class="form-control"
                        placeholder="correo@ejemplo.com"
                    >

                </div>


                <div class="form-group full">

                    <label>
                        Observaciones
                    </label>

                    <textarea
                        class="form-control"
                        rows="5"
                        placeholder="Información adicional..."
                    ></textarea>

                </div>

            </div>


            <div class="form-actions">

                <a
                    href="{{ route('alumnos.index') }}"
                    class="btn btn-outline"
                >
                    Cancelar
                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Guardar alumno →
                </button>

            </div>

        </div>

    </section>

</form>

@endsection