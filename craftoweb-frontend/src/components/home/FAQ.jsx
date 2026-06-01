import { useState } from 'react';
import { HiOutlinePlus, HiOutlineMinus } from 'react-icons/hi';
import { faqs } from '../../data/content';
import './FAQ.css';

function FAQ() {
  const [open, setOpen] = useState(0);

  return (
    <div className="faq-list">
      {faqs.map((item, i) => (
        <div key={item.q} className={`faq-item card-glass ${open === i ? 'open' : ''}`}>
          <button type="button" className="faq-trigger" onClick={() => setOpen(open === i ? -1 : i)}>
            <span>{item.q}</span>
            {open === i ? <HiOutlineMinus /> : <HiOutlinePlus />}
          </button>
          <div className="faq-answer">
            <p>{item.a}</p>
          </div>
        </div>
      ))}
    </div>
  );
}

export default FAQ;
