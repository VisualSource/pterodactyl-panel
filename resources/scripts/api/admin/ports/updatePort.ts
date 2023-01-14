import http from '@/api/http';
import { type Port, rawDataToPort } from '@/api/admin/ports/getPorts';

interface UpdateProps {}

export default (id: number, data: UpdateProps, include: string[] = []): Promise<Port> => {
    return new Promise((ok,rej)=>{
        http.patch(`/api/application/ports/${id}`,data,{
            params: {
                include: include.join(",")
            }
        }).then(({data})=>ok(rawDataToPort(data))).catch(rej);
    });
}
