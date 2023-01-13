import { useFormikContext } from 'formik';
import { useState } from 'react';

import type { Server } from "@/api/admin/servers/getServers";
import searchServers from '@/api/admin/servers/searchServers';
import SearchableSelect, { Option } from '@/components/elements/SearchableSelect';

export default ({ selected}: { selected: Server | null }) => {
    const context = useFormikContext();

    const [server,setServer] = useState<Server | null>(selected);
    const [servers,setServers] = useState<Server[] | null>(null);


    const onSearch = (query: string): Promise<void> => {
            return new Promise((resolve, reject) => {
                searchServers({ name: query })
                    .then(locations => {
                        setServers(locations);
                        return resolve();
                    })
                    .catch(reject);
            });
    };

    const onSelect = (location: Server | null) => {
        setServer(location);
        context.setFieldValue('server_id', location?.id || null);
    };

    const getSelectedText = (location: Server | null): string | undefined => {
        return `${location?.name} | ${location?.identifier}`
    };

    return (
        <SearchableSelect
            id={'server_id'}
            name={'server_id'}
            label={'Server'}
            placeholder={'Select a server...'}
            items={servers}
            selected={server}
            setSelected={setServer}
            setItems={setServers}
            onSearch={onSearch}
            onSelect={onSelect}
            getSelectedText={getSelectedText}
            nullable
        >
            {servers?.map(d => (
                <Option key={d.id} selectId={'server_id'} id={d.id} item={d} active={d.id === server?.id}>
                    {d.name} | {d.identifier}
                </Option>
            ))}
        </SearchableSelect>
    );
}
