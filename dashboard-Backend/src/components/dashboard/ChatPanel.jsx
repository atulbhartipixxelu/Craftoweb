import { useState } from 'react';
import { HiOutlinePaperAirplane } from 'react-icons/hi';
import { chatContacts, chatMessages } from '../../data/mockData';
import './ChatPanel.css';

function ChatPanel() {
  const [activeContact] = useState(chatContacts[0]);
  const [message, setMessage] = useState('');

  return (
    <div className="chat-panel">
      <div className="card chat-contacts-card">
        <ul className="contact-list">
          {chatContacts.map((contact) => (
            <li key={contact.id} className="contact-item">
              <img src={contact.avatar} alt={contact.name} className="avatar avatar-sm" />
              <div className="contact-info">
                <div className="contact-header">
                  <span className="contact-name">{contact.name}</span>
                  <span className="contact-time">{contact.time}</span>
                </div>
                <p className="contact-message">{contact.message}</p>
              </div>
              {contact.unread > 0 && (
                <span className="badge badge-sm">{contact.unread}</span>
              )}
            </li>
          ))}
        </ul>
      </div>

      <div className="card chat-window-card">
        <div className="chat-window-header">
          <img src={activeContact.avatar} alt={activeContact.name} className="avatar avatar-xs" />
          <span className="chat-partner-name">{activeContact.name}</span>
        </div>
        <div className="chat-messages">
          {chatMessages.map((msg) => (
            <div key={msg.id} className={`message-bubble message-${msg.sender}`}>
              {msg.text}
            </div>
          ))}
        </div>
        <div className="chat-input-area">
          <input
            type="text"
            placeholder="Alice is Typing..."
            value={message}
            onChange={(e) => setMessage(e.target.value)}
            className="chat-input"
          />
          <button className="send-btn" aria-label="Send message">
            <HiOutlinePaperAirplane />
          </button>
        </div>
      </div>
    </div>
  );
}

export default ChatPanel;
