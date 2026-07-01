<div class="section">

    <h2>
        Academic Summary
    </h2>

    <table>

        <tr>
            <th>
                Average Marks
            </th>

            <th>
                Failed Students
            </th>
        </tr>

        <tr>
            <td>
                {{ $academic['average_marks'] }}
            </td>

            <td>
                {{ $academic['failed_students'] }}
            </td>
        </tr>

    </table>

</div>

<h3>
    Top Students
</h3>

<table>

<tr>

    <th>ID</th>

    <th>Name</th>

    <th>Mark</th>

</tr>

@foreach($academic['top_students'] as $student)

<tr>

    <td>
        {{ $student->student_id }}
    </td>

    <td>
        {{ $student->student->full_name }}
    </td>

    <td>
        {{ $student->mark }}
    </td>

</tr>

@endforeach

</table>
