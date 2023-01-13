import http from '@/api/http';
import { type Domain, rawDataToDomain } from '@/api/admin/domains/getDomains';

export default (id: number, server_id: number | null, include: string[] = []): Promise<Domain> => {
    return new Promise((ok,rej)=>{
        http.patch(`/api/application/domains/${id}`,{
            server_id
        },{ params: { 
            include: include.join(",")
         } })
         .then(({ data })=> ok(rawDataToDomain(data)))
         .catch(rej)
    });
}