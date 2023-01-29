import { useFormikContext } from 'formik';
import { useState } from 'react';

import type { ServerLike } from '@/api/admin/domains/getDomains';
import type { Server } from '@/api/admin/servers/getServers';
import searchServers from '@/api/admin/servers/searchServers';
import SearchableSelect, { Option } from '@/components/elements/SearchableSelect';

export default ({ selected }: { selected: Server | ServerLike | null }) => {
    const context = useFormikContext();

    const [server, setServer] = useState<Server | ServerLike | null>(selected);
    const [servers, setServers] = useState<(Server | ServerLike)[] | null>(null);

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

    const onSelect = (location: Server | ServerLike | null) => {
        setServer(location);
        context.setFieldValue('server', location || null);
    };

    const getSelectedText = (location: Server | ServerLike | null): string | undefined => {
        if (!location) return;
        return `${location?.name} | ${location?.identifier}`;
    };

    return (
        <SearchableSelect
            id="server"
            name="server"
            label="Server"
            placeholder="Select a server..."
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
                <Option key={d.id} selectId={'server'} id={d.id} item={d} active={d.id === server?.id}>
                    {d.name} | {d.identifier}
                </Option>
            ))}
        </SearchableSelect>
    );
};
