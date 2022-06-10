@extends('templates/main')

@section('conteudo')
    <div class="row mb-2">
        <div class="col">
            <h3 class="display-7 text-secondary d-none d-md-block"><b>Docência </b>({{$ano}})</h3>
        </div>
    </div>    
    <div class="row mb-2">
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text bg-secondary text-white">Curso</span>
                <select 
                    onchange="loadAnos('{{$cursos}}');"
                    name="curso"
                    id="curso"
                    class="form-select @if($errors->has('area')) is-invalid @endif"
                >
                    @foreach ($cursos as $item)
                        <option value="{{$item->id}}" @if($item->id == old('curso')) selected="true" @endif>
                            {{ $item->sigla }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('curso'))
                    <div class='invalid-feedback'>
                        {{ $errors->first('curso') }}
                    </div>
                @endif
            </div>
        </div>
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text bg-secondary text-white">Ano</span>
                <select 
                    name="ano"
                    id="ano"
                    class="form-select @if($errors->has('ano')) is-invalid @endif"
                >
                    @for($a=1; $a<=$cursos[0]->tempo; $a++)
                        <option value="{{$a}}" @if($a == old('ano')) selected="true" @endif>
                            {{$a}}º          
                        </option>
                    @endfor
                </select>
                @if($errors->has('ano'))
                    <div class='invalid-feedback'>
                        {{ $errors->first('ano') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <a href="#" class="btn btn-secondary btn-block text-white" onclick="loadDados('{{$disciplinas}}', '{{$profs}}', '{{$data}}');">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#FFF" class="bi bi-box-arrow-down" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M3.5 10a.5.5 0 0 1-.5-.5v-8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 0 0 1h2A1.5 1.5 0 0 0 14 9.5v-8A1.5 1.5 0 0 0 12.5 0h-9A1.5 1.5 0 0 0 2 1.5v8A1.5 1.5 0 0 0 3.5 11h2a.5.5 0 0 0 0-1h-2z"/>
                <path fill-rule="evenodd" d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708l3 3z"/>
            </svg>
            &nbsp;<b>Carregar</b>
        </a>
    </div>
    <div class="row mb-3">
        <div class="col">
            <table class="table align-middle caption-top table-striped" id="tabela">
                <caption>Tabela de <b>Disciplinas / Professores</b></caption>
                <thead>
                <tr>
                    <th scope="col" class="text-center">Disciplina</th>
                    <th scope="col" class="text-center">Professor</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <form id="formulario">
        <input type="hidden" name="ano" id="ano_letivo" value="{{$ano}}">
        <div class="row">
            <div class="col-lg-4 col-md-12">
                <a href="{{route('professores.index')}}" class="btn btn-danger btn-block align-content-center w-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-left-square-fill" viewBox="0 0 16 16">
                        <path d="M16 14a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12zm-4.5-6.5H5.707l2.147-2.146a.5.5 0 1 0-.708-.708l-3 3a.5.5 0 0 0 0 .708l3 3a.5.5 0 0 0 .708-.708L5.707 8.5H11.5a.5.5 0 0 0 0-1z"/>
                    </svg>
                    &nbsp;<b>Cancelar</b>
                </a>
            </div>
            <div class="col-lg-8 col-md-12">
                <button class="btn btn-success btn-block text-white mb-2 w-100" style="display: none" type="submit" id="bt_salvar">
                    <b>Confirmar</b>&nbsp;
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                </button>
            </div>
        </div>
    </form>

    <div class="modal fade" tabindex="-1" id="infoModal">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
            <h5 class="modal-title text-white"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="removeModal" onclick="closeModal()" aria-label="Close"></button>
            </div>
            <input type="hidden" id="id_remove">
            <div class="modal-body text-secondary">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-block align-content-center" onclick="closeModal()">
                    OK
                </button>
            </div>
        </div>
        </div>
    </div>
@endsection

@section('script')

    <script type="text/javascript">

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        $("#formulario").submit( function(event){
            event.preventDefault();
            sendDados();
        });

        function closeModal() { $("#infoModal").modal('hide') } 

        function voltarNavegacao() { history.back(); }

        function loadAnos(data) {

            let curso;
            const cursos = JSON.parse(data);
            const val = $('#curso').val();

            cursos.forEach(function(item){
                if(item.id == val) {
                    curso = item;
                }
            })

            $('#ano').empty();
            for(a=1; a<=curso.tempo; a++) {
                $('#ano').append($('<option>', {
                    value: a,
                    text: a+'º'
                }));
            }
        }

        function loadDados(disciplinas, profs, data) {

            const disc = JSON.parse(disciplinas);
            const curso = $('#curso').val();
            const ano = $('#ano').val();
            
            // Limpa Tabela
            $('#tabela>tbody').empty();
            disc.forEach(function(item){
                // Disciplina do ano e curso selecionados
                if(item.curso_id == curso && item.periodo == ano) {
                    linha = "<tr class='text-center'><td>" +
                        item.nome + "</td><td>" + 
                        getSelect(JSON.parse(profs), item.id, JSON.parse(data)) + "</tr>";
                    $('#tabela>tbody').append(linha);
                }
            })

            $('#bt_salvar').show();
        }

        function getSelect(profs, disciplina_id, data) {

            var select_text = "<select class='form-select' name='profs'><option selected disabled></option>";
            
            profs.forEach(function(item) {
                select_text += "<option value='" + disciplina_id + "_" + item.id + "'";
                
                data.forEach(function(doc) {
                    if(item.id == doc.professor_id && disciplina_id == doc.disciplina_id) {
                        select_text += " selected='true'";    
                    }
                })
                
                select_text += ">" + item.nome + "</option>";
            })

            select_text +="</select>";
            
            return select_text;
        }

        function sendDados() {

            // javascript:document.querySelector('form').submit();

            var text = "";
            var sels = document.getElementsByTagName('select');

            if(verificaCampos(sels)) {
                for (let i = 0; i < sels.length; i++){
                    if(sels[i].getAttribute("name") == "profs") {
                        text += sels[i].value + " ";
                    }
                }

                $.ajax({
                    url: '/api/docencias',
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: text, 
                        ano: $("#ano_letivo").val()
                    },
                })
                .done(function(msg){
                    alert(msg)
                    $('#infoModal').modal().find('.modal-header').removeClass("bg-danger")
                    $('#infoModal').modal().find('.modal-header').addClass("bg-success")
                    $('#infoModal').modal().find('.modal-title').text("Professores / Disciplinas")
                    $('#infoModal').modal().find('.modal-body').text("Operação realizada com sucesso!")
                    $('#infoModal').modal().find('.modal-footer').find('button').removeClass("bg-danger")
                    $('#infoModal').modal().find('.modal-footer').find('button').addClass("bg-success")
                    $('#infoModal').modal('show')
                })
                .fail(function(jqXHR, textStatus, msg){
                    alert(msg)
                })

                // alert(text + " / " + $("#ano_letivo").val());
                // onclick="sendDados('{{$ano}}');"
            }
            else {
                $('#infoModal').modal().find('.modal-header').removeClass("bg-secondary");
                $('#infoModal').modal().find('.modal-header').addClass("bg-danger");
                $('#infoModal').modal().find('.modal-title').text("Preenchimento Obrigatório");
                $('#infoModal').modal().find('.modal-body').text("Por favor, selecione um professor de cada disciplina");
                $('#infoModal').modal().find('.modal-footer').find('button').removeClass("bg-secondary");
                $('#infoModal').modal().find('.modal-footer').find('button').addClass("bg-danger");
                $('#infoModal').modal('show')
            }
        }

        function verificaCampos(sels) {

            for (let i = 0; i < sels.length; i++){
                if(sels[i].getAttribute("name") == "profs") {
                    if(sels[i].value == "") 
                        return false;
                }
            }
            return true;
        }

    </script>
@endsection
