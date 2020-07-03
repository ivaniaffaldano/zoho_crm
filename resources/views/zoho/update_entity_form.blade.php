@php
function htmlFormType($param) {
    switch($param){
        case 'email':
            return 'email';
        case 'phone':
            return 'tel';
        case 'datetime':
            return 'datetime-local';
        case 'date':
            return 'date';
        default:
            return 'text';
    }
}
function htmlFormValue($entity,$fieldName) {
    if(count($entity)>0){
        if(isset($entity[0]["fields"][$fieldName])){
            return $entity[0]["fields"][$fieldName];
        }
    }
    return "";
}
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">

            <div class="content">

                <div class="main_content">
                    <h1>Update {{ $entityType }}</h1>
                    <form action="/api/updateEntity" method="post" name="updateEntity" target="_blank">
                        @csrf
                        <input type="hidden" name="_entityType" value="{{ $entityType }}"/>
                        <input type="hidden" name="_id" value="{{ $id }}"/>
                        @foreach ($fields as $field)
                            <label for="{{ $field['apiName'] }}">{{ $field['apiName'] }}:</label>
                            @if ($field['type'] === 'picklist')
                                <select id="{{ $field['apiName'] }}" name="{{ $field['apiName'] }}">
                                    @foreach ($field['pickList'] as $pickList)
                                        <option value="{{ $pickList['displayValue'] }}"
                                            @if ($pickList['displayValue'] == htmlFormValue($entity,$field['apiName']))
                                                selected="selected"
                                            @endif
                                        >{{ $pickList['displayValue'] }}</option>
                                    @endforeach
                                </select><br/><br/>
                            @else
                                <input type="{{ htmlFormType($field['type']) }}" name="{{ $field['apiName'] }}"
                                    value="{{ htmlFormValue($entity,$field['apiName']) }}"
                                    @if ($field['isMandatory'])
                                        required
                                    @endif
                                ><br/><br/>
                            @endif
                        @endforeach
                        <input type="submit" value="Submit">
                    </form>
                </div>

            </div>
        </div>
    </body>
</html>
