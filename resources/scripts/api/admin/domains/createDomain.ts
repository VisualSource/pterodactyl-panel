import http from '@/api/http';
import { type Domain, rawDataToDomain } from '@/api/admin/domains/getDomains';

export default (domain: string, server_id: number | null, include: string[] = []): Promise<Domain> => {
    return new Promise((ok, rej) => {
        http.post(
            '/api/application/domains',
            {
                domain,
                server_id,
            },
            {
                params: {
                    include: include.join(','),
                },
            },
        )
            .then(({ data }) => ok(rawDataToDomain(data)))
            .catch(rej);
    });
};
