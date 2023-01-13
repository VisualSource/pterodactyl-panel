import type { FormikHelpers } from 'formik';
import { Form, Formik } from 'formik';
import { useState } from 'react';
import tw from 'twin.macro';
import { object, number } from 'yup';
import { TrashIcon } from '@heroicons/react/outline';
import { Button } from '@/components/elements/button/index';
import { Size, Variant } from '@/components/elements/button/types';
import FlashMessageRender from '@/components/FlashMessageRender';
import useFlash from '@/plugins/useFlash';
import ServerSelect from '@/components/admin/domains/ServerSelect';
import getDomains from '@/api/admin/domains/getDomains';
import updateDomain from '@/api/admin/domains/updateDomain';
import deleteDomain from '@/api/admin/domains/deleteDomain';
import Field from '@/components/elements/Field';
import Modal from '@/components/elements/Modal';


interface Values {
    server_id: number | null;
}

const schema = object().shape({
    server_id: number().notRequired().nullable().integer("Server Id must be a integer.").positive("Must be a positive number.")
});

export default function DomainEditButton({ data }: any) {
    const [visible, setVisible] = useState<boolean>(false);
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { mutate } = getDomains(["server"]);

    const open = () => setVisible(true);
    const close = () => setVisible(false);

    const submit = ({ server_id }: Values, { setSubmitting }: FormikHelpers<Values>) => {
        clearFlashes("domain:update");
        setSubmitting(true);

        updateDomain(data.id,server_id,["server"])
        .then(async domain=>{
            await mutate(data => ({ ...data!, items: data!.items.concat(domain) }), false);
            setVisible(false);
        }).catch(error=>{
            clearAndAddHttpError({ key: "domain:update", error });
            setSubmitting(false);
        });
    }

    return (
        <>
            <Formik onSubmit={submit} validationSchema={schema} initialValues={{ server_id: null } as Values}>
                {({ isSubmitting })=>(
                    <Modal 
                    visible={visible} 
                    dismissable={!isSubmitting} 
                    showSpinnerOverlay={isSubmitting} 
                    onDismissed={close}>

                        <h2 css={tw`mb-6 text-2xl text-neutral-100`}>Edit Domain</h2>
                    </Modal>
                )} 
            </Formik>
            <button type="button" onClick={open} css={tw`text-primary-400 hover:text-primary-300`}>
                {data.domain}
            </button>
        </>
    );
}

/**
 * 
 * 
 * <Formik onSubmit={submit} initialValues={{ server_id: data.server_id }} validationSchema={schema}>
                {({ isSubmitting, resetForm })=>(
                    <Modal 
                        visible={visible}
                        dismissable={!isSubmitting}
                        showSpinnerOverlay={isSubmitting}
                        onDismissed={()=>{
                            resetForm();
                            setVisible(false);
                        }}
                    >
                        <FlashMessageRender byKey={'domain:update'} css={tw`mb-6`} />

                        <h2 css={tw`mb-6 text-2xl text-neutral-100`}>Edit Domain</h2>


                        <Form css={tw`m-0`}>
                            <Field
                                defaultValue={data.domain}
                                disabled
                                type={'text'}
                                id={'domain'}
                                name={'domain'}
                                label={'Domain'}
                                readOnly
                                placeholder="example"
                                description={'The subdomain that is to be registered.'}
                                autoFocus
                            />

                            <div css={tw`mt-6`}>
                                <ServerSelect selected={data.server ?? null}/>
                            </div>

                            <div css={tw`flex flex-wrap justify-end mt-6 gap-1`}>
                                <Button.Danger css={tw`mr-auto`} type="button">
                                    <TrashIcon css={tw`text-neutral-300 h-5 w-5`}/>
                                </Button.Danger>
                                
                                <Button.Text
                                    type="button"
                                    variant={Variant.Secondary}
                                    css={tw`w-full sm:w-auto sm:mr-2`}
                                    onClick={() => setVisible(false)}
                                >
                                    Cancel
                                </Button.Text>
                                <Button css={tw`w-full mt-4 sm:w-auto sm:mt-0`} type='submit'>
                                    Edit Domain
                                </Button>
                            </div>
                        </Form>
                    </Modal>
                )}
            </Formik>
 */