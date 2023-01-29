import http from '@/api/http';
import { type Server, rawDataToServer } from '@/api/admin/servers/getServers';

interface Filters {
    name?: string;
    uuidShort?: string;
    node_id?: number;
}

type FilterName = `filter[${keyof Filters}]`;

export default (filters?: Filters): Promise<Server[]> => {
    const params: Partial<Record<FilterName, any>> = {};

    if (filters) {
        Object.keys(filters).forEach(key => {
            params[`filter[${key}]` as FilterName] = filters[key as keyof typeof filters];
        });
    }

    return new Promise((ok, rej) => {
        http.get('/api/application/servers', { params })
            .then(resp => ok((resp.data.data ?? []).map(rawDataToServer)))
            .catch(rej);
    });
};
