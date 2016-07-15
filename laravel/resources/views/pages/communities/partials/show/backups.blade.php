<div class="block-loading-wrapper">
    <div class="community-downloads">
        <div class="table-responsive">
            <div class="success-message hide">Backup was deleted successfully!</div>
            <table class="table downloads-list-table">
                <thead>
                <tr>
                    <th class="text-left">Link</th>
                    <th>Created by</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                @if(count($backups))
                    @foreach($backups as $backup)
                        <tr>
                            <td>
                                <a href="{!! $backup->getS3Link() !!}">{{ pathinfo($backup->s3_key, PATHINFO_FILENAME) }}</a>
                            </td>
                            <td class="text-nowrap text-center">{{ cp_get_user_fullname($backup->user_id) }}</td>

                            <td class="text-nowrap text-center">{{ formatDate($backup->created_at, 'Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="empty-row ">No Test Data backups yet</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>